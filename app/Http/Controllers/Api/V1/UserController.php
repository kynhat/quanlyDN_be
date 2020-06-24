<?php

namespace App\Http\Controllers\Api\V1;

use App\Api\Entities\User;

use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\ShopRepository;
use App\Api\Repositories\Contracts\BranchRepository;

use \Firebase\JWT\JWT;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;

class UserController extends Controller
{
    /**
     * @var BranchRepository
     */
    protected $branchRepository;
    /**
     * @var DepartmentRepository
     */

   

    /**
     * @var UserRepository
     */
    protected $userRepository;


    protected $request;

    protected $auth;

    public function __construct(
        BranchRepository $branchRepository,
     
        UserRepository $userRepository,
       

        AuthManager $auth,
        Request $request)
    {
        $this->branchRepository = $branchRepository;
        $this->userRepository = $userRepository;
     
        $this->request = $request;
        $this->auth = $auth;

        parent::__construct();
    }

    /**
     * @api {get} /user 1. Current user info
     * @apiDescription (current user info)
     * @apiGroup user
     * @apiPermission JWT
     * @apiVersion 0.1.0
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "error_code": 0,
     * "message": [
     * "Successfully"
     * ],
     * "data": {
     * "id": "d8Wn2WmmjKnBtMRED",
     * "name": "Trung Hà",
     * "username": "+84909224002",
     * "email": "+84909224002@argi.com",
     * "phone": "+84909224002",
     * "phone_code": "84",
     * "is_supplier": 0,
     * "brandRepresent": "1",
     * "company": [
     * {
     * "name": "name",
     * "label": "Name",
     * "value": "GMA"
     * },
     * ]
     * }
     * }
     */
    public function login()
    {
        #region Validation
        $validator = \Validator::make($this->request->all(), [
            'name_login' => 'required',
            'phone_number' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $name_login = $this->request->get('name_login');
        $phone_number = $this->request->get('phone_number');
        #endregion
        #region Check shop exist
        $checkShopParams = [
            'is_detail' => 1,
            'name_login' => $name_login,
        ];
        // $shop = $this->userRepository->getShop($checkShopParams);
        // if (empty($shop)) {
        //     $checkUserParams = [
        //         'is_detail' => 1,
        //         'phone_number' => $phone_number,
        //     ];
        //     // $user = $this->userRepository->getUser($checkUserParams);
        //     // if (!empty($user)) {
        //     //     return $this->errorBadRequest('Số điện thoại đã thuộc một công ty khác');
        //     // }
        //     // return $this->errorBadRequest('Công ty không tồn tại');
        // }
        #region Check user exist
        $checkUserParams = [
            'is_detail' => 1,
            // 'shop_id' => $shop->_id,
            'phone_number' => $phone_number,
        ];
        $user = $this->userRepository->getUser($checkUserParams);
        if (empty($user)) {
            return $this->errorBadRequest('Số điện thoại không tồn tại');
        }
        #endregion
        #region Return Token
        $token = $this->auth->fromUser($user);
        return $this->successRequest($token);
        #endregion
    }    //Đăng nhập

    public function listUsers()
    {
        #region Check Au
        $user = $this->user();
        if ($user->is_root != 1) {
            return $this->errorBadRequest('Bạn không có quyền');
        }
        #endregion
        #region Input
        $shop_id = $user->shop_id;
        #endregion
        #region Get list department
        $params = [
            'is_paginate' => 1,
            'shop_id' => $shop_id
        ];
        $users = $this->userRepository->getUser($params);
        $item = [];
        foreach ($users as $user) {
            $user->shop_name=$user->shop()['shop_name'];
            if($user->is_root!=1)
            {
                $user->branch_name=$user->branch()['name'];
                $user->department_name=$user->department()['name'];
                $user->position_name=$user->position()['name'];
            }
            $item[] = $user->transform();
        }
        $data = [
            'items' => $item,
            'meta' => build_meta_paging($users)
        ];
        #endregion
        return $this->successRequest($data);
    } //Xem danh sách user (Quản lí)

    public function add()
    {
        #region Check Au
        $user = $this->user();
        if ($user->is_root != 1) {
            return $this->errorBadRequest('Bạn không có quyền');
        }
        #endregion
        #region Validation
        $validator = \Validator::make($this->request->all(), [
            'username' => 'required',
            'email' => 'email|required|string',
            'phone_number' => 'required|numeric',
            'branch_id' => 'required',
            'department_id' => 'required',
            'position_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $username = $this->request->get('username');
        $email = $this->request->get('email');
        $phone_number = $this->request->get('phone_number');
       
        $branch_id = $this->request->get('branch_id');
      
        #endregion
        #region Check duplicate email
        $checkParamsEmail = [
            'is_detail' => 1,
            'email' => $email,
        ];
        $CheckUserEmail = $this->userRepository->getUser($checkParamsEmail);
        if (!empty($CheckUserEmail)) {
            return $this->errorBadRequest("Email đã tồn tại");
        }
        #endregion
        #region Check duplicate phone_num
        $checkParamsPhone = [
            'is_detail' => 1,
            'phone_number' => $phone_number,
        ];
        $CheckUserPhone = $this->userRepository->getUser($checkParamsPhone);
        if (!empty($CheckUserPhone)) {
            return $this->errorBadRequest("Số điện thoại đã tồn tại");
        }
        #endregion
        #region Check exist Branch
        $checkBranch=[
            'is_detail'=>1,
            'id'=>$branch_id,
        ];
        $branch=$this->branchRepository->getBranch($checkBranch);
        if(empty($branch))
        {
            return $this->errorBadRequest('Chi nhánh không tồn tại');
        }
        #endregion
        #region Check exist Department
        // $checkDepartment=[
        //     'is_detail'=>1,
        //     'id'=>$department_id,
        // ];
        // $department=$this->departmentRepository->getDepartment($checkDepartment);
        // if(empty($department))
        // {
        //     return $this->errorBadRequest('Phòng ban không tồn tại');
        // }
        #endregion
        #region Check exist Position
        // $checkPosition=[
        //     'is_detail'=>1,
        //     'id'=>$position_id,
        // ];
        // $position=$this->positionRepository->getPosition($checkPosition);
        // if(empty($position))
        // {
        //     return $this->errorBadRequest('Chức danh không tồn tại');
        // }
        #endregion
        #region Insert User
        $attributes = [
            'username' => $username,
            'email' => $email,
            'phone_number' => $phone_number,
            'shop_id' => mongo_id($shop_id),
            'branch_id' => mongo_id($branch_id),
            // 'department_id' => mongo_id($department_id),
            // 'position_id' => mongo_id($position_id),
        ];
        $user = $this->userRepository->create($attributes);
        #endregion
        return $this->successRequest($user->transform());
    }    //Thêm user(Quản lí)

    public function delete()
    {
        #region Check Au
        $user = $this->user();
        if ($user->is_root != 1) {
            return $this->errorBadRequest('Bạn không có quyền');
        }
        #endregion
        #region Validation
        $validator = \Validator::make($this->request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $user_id = $this->request->get('user_id');
        #endregion
        #region Check exist
        $params = [
            'is_detail' => 1,
            'id' => $user_id,
        ];

        $user = $this->userRepository->getUser($params);
        if (empty($user)) {
            return $this->errorBadRequest('Nhân viên không tồn tại');
        }
        #endregion
        #region Delete
        $user->delete();
        #endregion
        return $this->successRequest('Xoá nhân viên thành công');
    }  //Xoá user (Quản lí)

    // public function edit()
    // {
    //     #region Check Au
    //     $userAu = $this->user();
    //     if ($userAu->is_root != 1)
    //     {
    //         #region Validation
    //         $validator = \Validator::make($this->request->all(), [
    //             'username' => 'required',
    //             'email' => 'email|required|string',
    //         ]);
    //         if ($validator->fails()) {
    //             return $this->errorBadRequest($validator->messages()->toArray());
    //         }
    //         #endregion
    //         #region Input
    //         $user_id = $userAu->_id;
    //         $username = $this->request->get('username');
    //         $email = $this->request->get('email');
    //         #endregion
    //         #region Check exist
    //         $params = [
    //             'is_detail' => 1,
    //             'id' => $user_id,
    //         ];
    //         $user = $this->userRepository->getUser($params);
    //         if (empty($user)) {
    //             return $this->errorBadRequest('Nhân viên không tồn tại');
    //         }
    //         #endregion
    //         #region Check duplicate email
    //         $checkParamsEmail = [
    //             'is_detail' => 1,
    //             'email' => $email,
    //             'exclude_id' => $user->_id,
    //         ];
    //         $CheckUserEmail = $this->userRepository->getUser($checkParamsEmail);
    //         if (!empty($CheckUserEmail)) {
    //             return $this->errorBadRequest("Email đã tồn tại");
    //         }
    //         #endregion
    //         #region Update
    //         $user->username = $username;
    //         $user->email = $email;
    //         $user->save();
    //         #endregion
    //         return $this->successRequest($user->transform());
    //     }
    //     #region Validation
    //     $validator = \Validator::make($this->request->all(), [
    //         'user_id' => 'required',
    //         'username' => 'required',
    //         'email' => 'email|required|string',
    //         'phone_number' => 'required|numeric',
    //         'branch_id' => 'required',
    //         'department_id' => 'required',
    //         'position_id' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return $this->errorBadRequest($validator->messages()->toArray());
    //     }
    //     #endregion
    //     #region Input
    //     $user_id = $this->request->get('user_id');
    //     $username = $this->request->get('username');
    //     $email = $this->request->get('email');
    //     $phone_number = $this->request->get('phone_number');
    //     $branch_id = $this->request->get('branch_id');
    //     $department_id = $this->request->get('department_id');
    //     $position_id = $this->request->get('position_id');
    //     #endregion
    //     #region Check exist
    //     $params = [
    //         'is_detail' => 1,
    //         'id' => $user_id,
    //     ];
    //     $user = $this->userRepository->getUser($params);
    //     if (empty($user)) {
    //         return $this->errorBadRequest('Nhân viên không tồn tại');
    //     }
    //     #endregion
    //     #region Check duplicate email
    //     $checkParamsEmail = [
    //         'is_detail' => 1,
    //         'email' => $email,
    //         'exclude_id' => $user->_id,
    //     ];
    //     $CheckUserEmail = $this->userRepository->getUser($checkParamsEmail);
    //     if (!empty($CheckUserEmail)) {
    //         return $this->errorBadRequest("Email đã tồn tại");
    //     }
    //     #endregion
    //     #region Check duplicate phone_num
    //     $checkParamsPhone = [
    //         'is_detail' => 1,
    //         'phone_number' => $phone_number,
    //         'exclude_id' => $user->_id,
    //     ];
    //     $CheckUserPhone = $this->userRepository->getUser($checkParamsPhone);
    //     if (!empty($CheckUserPhone)) {
    //         return $this->errorBadRequest("Số điện thoại đã tồn tại");
    //     }
    //     #endregion
    //     #region Update
    //     $user->branch_id = mongo_id($branch_id);
    //     $user->department_id = mongo_id($department_id);
    //     $user->position_id = mongo_id($position_id);
    //     $user->username = $username;
    //     $user->email = $email;
    //     $user->phone_number = $phone_number;
    //     $user->save();
    //     #endregion
    //     return $this->successRequest($user->transform());
    // }   /*Chỉnh sửa thông tin User(Quản lí,Nhân Viên-chỉ được
    //                                chỉnh sửa tên và email)*/
    public function userShow()
    {
        $user = $this->user();
        $data = $user->transform('with-shop');

        //Save history login
        $date = Carbon::now();
        $user->visited_date = $date;
        $user->vistied_ip = get_client_ip();
        $user->save();
        return $this->successRequest($data);
    }

    /**
     * @api {post}/user/update 2. update my info
     * @apiDescription Update my info
     * @apiGroup user
     * @apiPermission JWT
     * @apiVersion 0.1.0
     * @apiParam {String} [name] name
     * @apiParam {String} [email] name
     * @apiParam {Object} [company] company[phone], company[address]...
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     * "error_code": 0,
     * "message": [
     * "Successfully"
     * ],
     * "data": {
     * "id": "p5jFuwDbo84KteeCc",
     * "name": "Trung Hà",
     * "username": "+84909224002",
     * "phone": "+84909224002",
     * "phone_code": "84",
     * "company": {
     * "phone": "0909090909",
     * "address": "Bùi Hữu Nghĩa, Bình Thạnh"
     * },
     * "is_supplier": 0
     * }
     * }
     */
    public function update(Request $request)
    {
        // Send email when user register supplier
        // $params = ['email' => 'onclick.trungha@gmail.com',
        //            'full_name' => 'Trung Hà',
        //            'subject' => 'Đăng ký làm đại lý trên FAMA'];
        // var_dump($this->userRepository->sendMailActiveSupplier($params));return;

        $entityUser = new User;
        $fillableList = $entityUser->getFillable();

        $userId = $this->user->id;
        $user = $this->userRepository->findByField('_id', $userId)->first();

        foreach ($fillableList as $key => $value) {
            if ($value == 'company') {
                if (!empty($this->request->get('company'))) {
                    if (empty($user->company)) {
                        $user->company = $this->request->get('company');
                    } else {
                        $company = $user->company;
                        foreach ($this->request->get('company') as $k => $v) {
                            $company[$k] = $v;
                        }
                        $user->company = $company;
                    }
                }

            } elseif ($value == 'email') {
                if (!empty($this->request->get('email'))) {
                    $emails = $user->emails;
                    $emails[0]['address'] = $this->request->get('email');
                    $user->emails = $emails;
                }
            } else {
                if (!empty($this->request->get($value)) || ($this->request->get($value) == 0 && $value != 'emails')) {
                    $user->$value = $this->request->get($value);
                }
            }
        }
        $user->save();
        return $this->successRequest($user->transform());
    }

    /**
     * @api {GET} /user/info/{username} 3. User Info
     * @apiDescription Get user info
     * @apiGroup user
     * @apiPermission JWT
     * @apiVersion 0.1.0
     * @apiParam {String} username  username's user
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *      "error_code": 0,
     * "message": [
     * "Successfully"
     * ],
     * "data": [
     * {
     * "id": "oGpZf8tSv3FNLHZv4",
     * "name": "saritvn",
     * "username": "saritvn",
     * "phone": "0909224002",
     * "phone_code": "84",
     * "company": {
     * "name": "Green Mobile App",
     * "address": "195 Dien Bien Phu, Ward 15, Binh Thanh Distric, Ho Chi Minh City",
     * "email": "trung.ha@greenapp.vn",
     * "phone": "0909224002",
     * "field": "Mobile App"
     * }
     * }
     * ]
     *     }
     */

    public function info(Request $request, $username)
    {
        // Validate HEADER import.
        // $validator = \Validator::make($request->all(), [
        //     'username'   => 'required',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->messages()->toArray());
        // }

        $user = $this->userRepository->findByField('username', $username)->first();
        if (empty($user)) {
            return $this->successRequest([]);
        }
        $data = $user->transform();
        return $this->successRequest($data);
    }
}
