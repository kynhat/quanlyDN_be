<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\NguoidungTransformer;
use Moloquent\Eloquent\SoftDeletes;

class Nguoidung extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'Nguoidung';

    protected $guarded = array();

    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    // public function transform()
    //  {
    //      $transformer = new NguoidungTransformer();
    //      return $transformer->transform($this);
    //  }
 public function transform()
    {
        return [
            
            'id'=>$this->_id,
            'tennd' =>$this->tennd,
            'MaND'=>$this->MaND,
            'SDT'=>$this->SDT,
            'Songuoi'=>$this->Songuoi,
            'gioitinh'=>$this->gioitinh,
            'ngaydat'=>$this->ngaydat,
            'ngayhuy'=>$this->ngayhuy,     
        ];
    }
}