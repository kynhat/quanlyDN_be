<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\phongRepository;
use App\Api\Entities\Phong;
use App\Api\Validators\PhongValidator;
use App\Api\Criteria\PhongCriteria;
/**
 * Class PhongRepositoryEloquent
 */
class PhongRepositoryEloquent extends BaseRepository implements PhongRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Phong::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
     public function getPhong($params = [],$limit = 0) {
        $this->pushCriteria(new PhongCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new PhongCriteria($params));
        return $item;
    }
}
