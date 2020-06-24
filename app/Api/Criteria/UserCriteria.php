<?php

namespace App\Api\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
/**
 * Class UserCriteria
 */
class UserCriteria implements CriteriaInterface
{
    protected $params;
    public function __construct($params = [])
    {
        $this->params = $params;
    }
    
    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $query = $model->newQuery();

        //Set language
        // $query->where('lang',app('translator')->getLocale());

       // $query->where('shop_id',mongo_id(Auth::getPayload()->get('shop_id')));
        if(!empty($this->params['id'])) {
            $query->where('_id',mongo_id($this->params['id']));
        }
        if(!empty($this->params['shop_id'])) {
            $query->where('shop_id',mongo_id($this->params['shop_id']));
        }
        if(!empty($this->params['name_login'])) {
            $query->where('name_login',$this->params['name_login']);
        }
        if(!empty($this->params['exclude_id'])) {
            $query->where('_id', '<>', mongo_id($this->params['exclude_id']));
        }
        if(!empty($this->params['email'])) {
            $query->where('email',$this->params['email']);
        }
        if(!empty($this->params['phone_number'])) {
            $query->where('phone_number',$this->params['phone_number']);
        }
        return $query;
    }
}
