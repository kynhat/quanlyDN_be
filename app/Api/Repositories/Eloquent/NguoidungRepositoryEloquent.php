<?php

namespace App\Api\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Repositories\Contracts\nguoidungRepository;
use App\Api\Entities\Nguoidung;
use App\Api\Validators\NguoidungValidator;
use App\Api\Criteria\NguoidungCriteria;
/**
 * Class NguoidungRepositoryEloquent
 */
class NguoidungRepositoryEloquent extends BaseRepository implements NguoidungRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Nguoidung::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
       public function getNguoidung($params = [],$limit = 0) {
        $this->pushCriteria(new NguoidungCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new NguoidungCriteria($params));
        return $item;
    }
}
