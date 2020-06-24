<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface UserRepository
 */
interface UserRepository extends RepositoryInterface
{
    public function getUser($params = [],$limit = 0);
}
