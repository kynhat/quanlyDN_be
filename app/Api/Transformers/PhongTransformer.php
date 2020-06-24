<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Phong;

/**
 * Class BranchTransformer
 */
class PhongTransformer extends TransformerAbstract
{

    public function transform(Phong $model)
    {
        return [
            'id'=>$model->_id,
            'tenphong' =>$model->tenphong,
            'giaphong'  =>$model->giaphong,
            'giadien' => $model ->giadien,
            'gianuoc' => $model ->gianuoc,
            'tongtien'=>$model ->tongtien,
            'trangthai'->$model->trangthai

            
        ];
    }
}
