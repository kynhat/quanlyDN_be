<?php

namespace App\Api\Repositories\Eloquent;

use App\Api\Criteria\UserCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Api\Repositories\Contracts\UserRepository;
use App\Api\Entities\User;
use App\Api\Validators\UserValidator;

/**
 * Class UserRepositoryEloquent
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    public function getUser($params = [], $limit = 0)
    {
        $this->pushCriteria(new UserCriteria($params));
        if(!empty($params['is_detail'])) {
            $item = $this->get()->first();
        } elseif(!empty($params['is_paginate'])) {
            $item = $this->paginate();
        } else {
            $item = $this->all();
        }
        $this->popCriteria(new UserCriteria($params));
        return $item;
    }
}
