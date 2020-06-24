<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\Branch;

/**
 * Class BranchTransformer
 */
class BranchTransformer extends TransformerAbstract
{

    public function transform(Branch $model)
    {
        return [
            'id'=>$model->_id,
            'name' => $model->name,
            'area'=>$model->area,
            'note'=>$model->note,
            'shop_id'=>mongo_id_string($model->shop_id),
        ];
    }
}
