<?php


namespace App\Http\Controllers\Api\V1;

use App\Api\Repositories\Contracts\NguoidungRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\AuthManager;
use Gma\Curl;
use App\Api\Entities\Shop;
use App\Api\Entities\User;
use App\Api\Entities\Nguoidung;
//Google firebase
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Firebase\Auth\Token\Exception\InvalidToken;

use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Nullable;

class NguoidungController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var BranchRepository
     */
    protected $NguoidungRepository;

    protected $auth;

    protected $request;

    public function __construct(
        UserRepository $userRepository,
        NguoidungRepository $nguoidungRepository,
        AuthManager $auth,
        Request $request
    )
    {
        $this->userRepository = $userRepository;
        $this->nguoidungRepository = $nguoidungRepository;
        $this->request = $request;
        $this->auth = $auth;
        //  parent::__construct();
    }


    public function listNguoidung()
    {

    // $id = $this->request->get('_id');
    // $tennd = $this->request->get('tennd');
    // $MaND = $this->request->get('MaND');
    // $SDT = $this->request->get('SDT');
    // $gioitinh = $this->request->get('gioitinh');
    // $ngaydat = $this->request->get('ngaydat');
    // $ngayhuy = $this->request->get('ngayhuy');

         $params = [
        'is_paginate'=>1,

       
    ];
    if(!empty($id))
    {
        $params['_id'] = $id;

    }
    if(!empty($tennd))
    {

        $params['tennd']=$tennd;

    }
    if(!empty($MaND))
    {

        $params['MaND']=$MaND;
        
    }
    if(!empty($SDT))
    {

        $params['SDT']=$SDT;

    }
    if(!empty($gioitinh))
    {

        $params['gioitinh']=$gioitinh;
        
    }
    if(!empty($ngaydat))
    {

        $params['ngaydat']=$ngaydat;
    }
      if(!empty($ngayhuy))
    {

        $params['ngayhuy']=$ngayhuy;
    }


    $nguoidungs = $this->nguoidungRepository->getNguoidung($params);
    $item=[];
    foreach ($nguoidungs as $nguoidung ) {
        $item[]=$nguoidung->transform();
    }
    // dd($nguoidungs);

    $data=[
        'item'=>$item,
        'meta'=>build_meta_paging($nguoidungs)
        
    ];
      // dd($data)
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

           'tennd' => 'required|string',
            'MaND' => 'required|numeric',
            'SDT' => 'required|numeric',
          'Songuoi' => 'required|numeric',
            'gioitinh' => 'required|string',
              'ngaydat' => 'required|date',
                  'ngayhuy' => 'required|date',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        $id = $this->request->get('id');
        $tennd = $this->request->get('tennd');
        $MaND = $this->request->get('MaND');
        $SDT = $this->request->get('SDT');
        $Songuoi = $this->request->get('Songuoi');
        $gioitinh = $this->request->get('gioitinh');
        $ngaydat = $this->request->get('ngaydat');
        $ngayhuy = $this->request->get('ngayhuy');
        #endregion
        #endregion
        #region Check exist
        $params = [
            'is_detail' => 1,
            'id' => $id
        ];
        $nguoidung = $this->nguoidungRepository->getNguoidung($params);
        if (empty($nguoidung)) {
            return $this->errorBadRequest('Chi nhánh không tồn tại');
        }
        #endregion
        #region Update
        $nguoidung->tennd = $tennd;
         $nguoidung->MaND = $MaND;
           $nguoidung->SDT = $SDT;
             $nguoidung->Songuoi = $Songuoi;
               $nguoidung->gioitinh = $gioitinh;
                 $nguoidung->ngaydat = $ngaydat;
                   $nguoidung->ngayhuy = $ngayhuy;
        if (!empty($id))
        $nguoidung->id = $id;
        $nguoidung->save();
        #endregion
        return $this->successRequest($nguoidung->transform());
    }    //Sửa thông tin chi nhánh (Quản lí)

    public function delete()
    {
        #region Check Au
        // $user = $this->user();
        // if ($user->is_root != 1) {
        //     return $this->errorBadRequest('Bạn không có quyền');
        // }
        #endregion
        #region Validation
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
        $nguoidung = $this->nguoidungRepository->getNguoidung($params);
        if (empty($nguoidung)) {
            return $this->errorBadRequest('Chi nhánh không tồn tại');
        }
        #endregion
        #region Delete
        $nguoidung->delete();
        #endregion
        return $this->successRequest('Xoá chi nhánh thành công');
    }    //Xoá chi nhánh  (Quản lí)

    public function add()
    {
        #region Check Au
        // $user = $this->user();
        // if ($user->is_root != 1) {
        //     return $this->errorBadRequest('Bạn không có quyền');
        // }
        #endregion
        #region Validation
        $validator = \Validator::make($this->request->all(), [
            'tennd' => 'required|string',
            'MaND' => 'required|numeric',
            'SDT' => 'required|numeric',
          'Songuoi' => 'required|numeric',
            'gioitinh' => 'required|string',
              'ngaydat' => 'required|date',
                  'ngayhuy' => 'required|date',
            // 'shop_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->messages()->toArray());
        }
        #endregion
        #region Input
        // $shop_id = $user->shop_id;
        $tennd = $this->request->get('tennd');
        $MaND = $this->request->get('MaND');
        $SDT = $this->request->get('SDT');
         $Songuoi = $this->request->get('Songuoi');
          $gioitinh = $this->request->get('gioitinh');
           $ngaydat = $this->request->get('ngaydat');
            $ngayhuy = $this->request->get('ngayhuy');
        #endregion
        #region Insert Branch
        $attributes = [
            'tennd' => $tennd,
            'MaND' => $MaND,
            'SDT' => $SDT,
            'Songuoi' => $Songuoi,
            'gioitinh'=>$gioitinh,
            'ngaydat'=>$ngaydat,
            'ngayhuy'=>$ngayhuy
         
        ];
        // if(!empty($songuoi))
        // {
        //     $attributes['songuoi']=$songuoi;
        // }
        $nguoidung = $this->nguoidungRepository->create($attributes);
        #endregion
        return $this->successRequest($nguoidung->transform());
    }       //Thêm chi nhánh  (Quản lí)
}