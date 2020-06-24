<?php

namespace App\Api\Transformers;

use League\Fractal\TransformerAbstract;
use App\Api\Entities\User;

/**
 * Class UserTransformer
 */
class UserTransformer extends TransformerAbstract
{

    public function transform(User $model)
    {
        return [
            'id'=>$model->_id,
            'username' => $model->username,
            'email'=>$model->email,
            'phone_number'=>$model->phone_number,
            'is_root'=>$model->is_root,
            'shop_id'=>mongo_id_string($model->shop_id),
            'branch_id'=>mongo_id_string($model->branch_id),
            'department_id'=>mongo_id_string($model->department_id),
            'position_id'=>mongo_id_string($model->position_id),
            'shop_name'=>$model->shop_name,
            'branch_name'=>$model->branch_name,
            'department_name'=>$model->department_name,
            'position_name'=>$model->position_name,
        ];
    }
}
