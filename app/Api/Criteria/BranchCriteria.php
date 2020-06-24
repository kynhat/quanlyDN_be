<?php

namespace App\Api\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
/**
 * Class BranchCriteria
 */
class BranchCriteria implements CriteriaInterface
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
        if(!empty($this->params['id'])) {
            $query->where('_id',mongo_id($this->params['id']));
        }
        if(!empty($this->params['name'])) {
            $query->where('name',$this->params['name']);
        }
        if(!empty($this->params['shop_id'])) {
            $query->where('shop_id',mongo_id($this->params['shop_id']));
        }
        if(!empty($this->params['exclude_id'])) {
            $query->where('_id', '<>', mongo_id($this->params['exclude_id']));
        }
        return $query;
    }
}
