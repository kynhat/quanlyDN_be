<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\BranchTransformer;
use Moloquent\Eloquent\SoftDeletes;

class Branch extends Moloquent
{
    use SoftDeletes;

    protected $collection = 'branches';

    protected $guarded = array();

    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function transform()
     {
         $transformer = new BranchTransformer();
         return $transformer->transform($this);
     }

}
