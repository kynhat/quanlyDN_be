<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\PhongTransformer;
use Moloquent\Eloquent\SoftDeletes;

class Phong extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'phong';

    protected $guarded = array();

    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
 public function transform()
    {
        return [
            
            'id'=>$this->_id,
             'tenphong' => $this->tenphong,
            'giaphong' => $this->giaphong,
            'giadien' =>  $this->giadien,
            'gianuoc' =>  $this->gianuoc,
            'tongtien' => $this->tongtien,
            'trangthai' =>$this->trangthai 
        ];
    }

}
