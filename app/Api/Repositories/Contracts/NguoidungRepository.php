<?php

namespace App\Api\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface NguoidungRepository
 */
interface NguoidungRepository extends RepositoryInterface
{
     public function getNguoidung($params = [],$limit = 0);
}
