<?php


namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\PhongRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;

use App\Api\Entities\Phong;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Nullable;

class PhongController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var BranchRepository
     */
    protected $phongRepository;

    protected $auth;

    protected $request;

    public function __construct(
        UserRepository $userRepository,
        PhongRepository $phongRepository,
        AuthManager $auth,
        Request $request
    )
    {
        $this->userRepository = $userRepository;
        $this->phongRepository = $phongRepository;
        $this->request = $request;
        $this->auth = $auth;
        //  parent::__construct();
    }


    public function listPhong()
    {

        // #region Check Au
      
        $params = [
            'is_paginate' => 1,
        ];
        $phongs = $this->phongRepository->getPhong($params);
        $item = [];
        foreach ($phongs as $phong) {
            $item[] = $phong->transform();
        }
        $data = [
            'items' => $item,
            'meta' => build_meta_paging($phongs)
        ];
        // #endregion
        return $this->successRequest($data);
    }   //Xem danh sách chi nhánh (Quản lí)

    public function edit()
    {
        #region Check Au
        // $user = $this->user();
        // if ($user->is_root != 1) {
        //     return $this->errorBadRequest('Bạn không có quyền');
        // }
        #endregion
        #region Validation
        $validator = \Validator::make($this->request->all(), [
           'tenphong' => 'required|string',
            'giaphong' => 'required|numeric',
            'giadien' => 'required|numeric',
          'gianuoc' => 'required|numeric',
            'tongtien' => 'required|numeric',
              'trangthai' => 'required|string',
                
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $id = $this->request->get('id');
        $tenphong = $this->request->get('tenphong');
        $giaphong = $this->request->get('giaphong');
        $giadien = $this->request->get('giadien');
        $gianuoc = $this->request->get('gianuoc');
        $tongtien = $this->request->get('tongtien');
        $trangthai = $this->request->get('trangthai');
       
        #endregion
        #endregion
        #region Check exist
        $params = [
            'is_detail' => 1,
            'id' => $id
        ];
        $phong = $this->phongRepository->getPhong($params);
        if (empty($phong)) {
            return $this->errorBadRequest('Chi nhánh không tồn tại');
        }
        #endregion
        #region Update
        $phong->tenphong = $tenphong;
         $phong->giaphong = $giaphong;
        $phong->giadien = $giadien;
        $phong->gianuoc = $gianuoc;
        $phong->tongtien = $tongtien;
        $phong->trangthai = $trangthai;

        if (!empty($id))
        $phong->id = $id;
        $phong->save();
        #endregion
        return $this->successRequest($phong->transform());
    }    //Sửa thông tin chi nhánh (Quản lí)

    public function delete()
    {
    
        $validator = \Validator::make($this->request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $id = $this->request->get('id');
        #endregion
        #region Check exist
        $params = [
            'is_detail' => 1,
            'id' => $id,
        ];
        $phong = $this->phongRepository->getPhong($params);
        if (empty($phong)) {
            return $this->errorBadRequest('Chi nhánh không tồn tại');
        }
        #endregion
        #region Delete
        $phong->delete();
        #endregion
        return $this->successRequest('Xoá chi nhánh thành công');
    }    //Xoá chi nhánh  (Quản lí)

    public function add()
    {
        $validator =\Validator::make($this->request->all(),[
            'tenphong' =>'required|string',
            'giaphong' =>'required|numeric',
            'giadien' => 'required|numeric',
            'gianuoc' => 'required|numeric',
            'tongtien'=>'required|numeric',
            'trangthai' => 'required|string'

        ]);
          if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
       
        $tenphong = $this->request->get('tenphong');
        $giaphong = $this->request->get('giaphong');
        $gianuoc = $this->request->get('gianuoc');
        $giadien=   $this->request->get('giadien');
        $tongtien= $this->request->get('tongtien');
        $trangthai= $this->request->get('trangthai');
       
    
        $attributes = [
            'tenphong' => $tenphong,
            'giaphong' => $giaphong,
            'giadien' =>  $giadien,
            'gianuoc' =>  $gianuoc,
            'tongtien' => $tongtien,
            'trangthai' =>$trangthai
                
         
        ];
        // if(!empty($songuoi))
        // {
        //     $attributes['songuoi']=$songuoi;
        // }
        $phong = $this->phongRepository->create($attributes);
        #endregion
        return $this->successRequest($phong->transform());  
    }
}