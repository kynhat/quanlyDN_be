<?php

namespace App\Api\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
/**
 * Class UserShiftCriteria
 */
class UserShiftCriteria implements CriteriaInterface
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
        if (!empty($this->params['ids'])) {
            $query->whereIn('_id',$this->params['ids']);
        }
        if (!empty($this->params['id'])) {
            $query->where('_id', mongo_id($this->params['id']));
        }
        if (!empty($this->params['user_id'])) {
            $query->where('user_id', mongo_id($this->params['user_id']));
        }
        if (!empty($this->params['shift_id'])) {
            $query->where('shift_id', mongo_id($this->params['shift_id']));
        }
        if(!empty($this->params['exclude_id'])) {
            $query->where('_id', '<>', mongo_id($this->params['exclude_id']));
        }
        return $query;
    }
}
