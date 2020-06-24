<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface PhongRepository
 */
interface PhongRepository extends RepositoryInterface
{
     public function getPhong($params = [],$limit = 0);
}
