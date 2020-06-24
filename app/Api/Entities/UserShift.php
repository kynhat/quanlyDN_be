<?php

namespace App\Api\Entities;

use Moloquent\Eloquent\Model as Moloquent;
use App\Api\Transformers\UserShiftTransformer;
use Moloquent\Eloquent\SoftDeletes;

class UserShift extends Moloquent
{
	use SoftDeletes;

	protected $collection = 'user_shifts';

    protected $guarded = array();

    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function transform()
    {
        $transformer = new UserShiftTransformer();

        return $transformer->transform($this);
    }
}
