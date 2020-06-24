<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Nguoidung;

/**
 * Class BranchTransformer
 */
class NguoidungTransformer extends TransformerAbstract
{

    public function transform(Nguoidung $model)
    {
        return [
            'id'=>$model->_id,
            'tennd' =>$model->tennd,
            'MaND'=>$model->MaND,
            'SDT'=>$model->SDT,
            'Songuoi'=>$model->Songuoi,
            'gioitinh'=>$model->gioitinh,
            'ngaydat'=>$model->ngaydat,
            'ngayhuy'=>$model->ngayhuy,     
            
        ];
    }
}
