<?php

namespace App\Api\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;
/**
 * Class NguoidungCriteria
 */
class NguoidungCriteria implements CriteriaInterface
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

        // $query->where('lang',app('translator')->getLocale());
        if (!empty($this->params['ids'])) {
            $query->whereIn('_id',$this->params['ids']);
        }
        if (!empty($this->params['id'])) {
            $query->where('_id', mongo_id($this->params['id']));
        }
        return $query;
    }
}
