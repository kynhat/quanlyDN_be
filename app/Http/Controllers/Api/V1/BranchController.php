<?php


namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\BranchRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Shop;
use App\Api\Entities\User;
use App\Api\Entities\Branch;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Nullable;

class BranchController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var BranchRepository
     */
    protected $branchRepository;

    protected $auth;

    protected $request;

    public function __construct(
        UserRepository $userRepository,
        BranchRepository $branchRepository,
        AuthManager $auth,
        Request $request
    )
    {
        $this->userRepository = $userRepository;
        $this->branchRepository = $branchRepository;
        $this->request = $request;
        $this->auth = $auth;
        //  parent::__construct();
    }


    public function listBranches()
    {
        #region Check Au
        $user = $this->user();
        if ($user->is_root != 1) {
            return $this->errorBadRequest('Bạn không có quyền');
        #endregion
        }
        #region Input
        $shop_id = $user->shop_id;
        #endregion
        #region Get list branch
        $params = [
            'is_paginate' => 1,
            'shop_id' => $shop_id,
        ];
        $branches = $this->branchRepository->getBranch($params);
        $item = [];
        foreach ($branches as $branch) {
            $item[] = $branch->transform();
        }
        $data = [
            'items' => $item,
            'meta' => build_meta_paging($branches)
        ];
        #endregion
        return $this->successRequest($data);
    }   //Xem danh sách chi nhánh (Quản lí)

    public function edit()
    {
        #region Check Au
        $user = $this->user();
        if ($user->is_root != 1) {
            return $this->errorBadRequest('Bạn không có quyền');
        }
        #endregion
        #region Validation
        $validator = \Validator::make($this->request->all(), [
            'branch_id' => 'required',
            'name' => 'required|string',
            'area' => 'required|string',
            'note' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $id = $this->request->get('branch_id');
        $name = $this->request->get('name');
        $area = $this->request->get('area');
        $note = $this->request->get('note');
        #endregion
        #region Check exist
        $params = [
            'is_detail' => 1,
            'id' => $id
        ];
        $branch = $this->branchRepository->getBranch($params);
        if (empty($branch)) {
            return $this->errorBadRequest('Chi nhánh không tồn tại');
        }
        #endregion
        #region Update
        $branch->name = $name;
        $branch->area = $area;
        if (!empty($note))
        $branch->note = $note;
        $branch->save();
        #endregion
        return $this->successRequest($branch->transform());
    }    //Sửa thông tin chi nhánh (Quản lí)

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
            'branch_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $id = $this->request->get('branch_id');
        #endregion
        #region Check exist
        $params = [
            'is_detail' => 1,
            'id' => $id,
        ];
        $branch = $this->branchRepository->getBranch($params);
        if (empty($branch)) {
            return $this->errorBadRequest('Chi nhánh không tồn tại');
        }
        #endregion
        #region Delete
        $branch->delete();
        #endregion
        return $this->successRequest('Xoá chi nhánh thành công');
    }    //Xoá chi nhánh  (Quản lí)

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
            'name' => 'required|string',
            'area' => 'required|string',
            'note' => 'nullable',
            // 'shop_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $shop_id = $user->shop_id;
        $name = $this->request->get('name');
        $area = $this->request->get('area');
        $note = $this->request->get('note');
        #endregion
        #region Insert Branch
        $attributes = [
            'name' => $name,
            'area' => $area,
            'shop_id' => mongo_id($shop_id),
        ];
        if(!empty($note))
        {
            $attributes['note']=$note;
        }
        $branch = $this->branchRepository->create($attributes);
        #endregion
        return $this->successRequest($branch->transform());
    }       //Thêm chi nhánh  (Quản lí)
}