<?php

namespace App\Api\Entities;

use App\Api\Transformers\UserTransformer;
use Dingo\Api\Http\Request;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Moloquent\Eloquent\Model as Moloquent;
use Moloquent\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;

class User extends Moloquent implements AuthenticatableContract, JWTSubject
{
    use Authenticatable, SoftDeletes;
    protected $collection = 'users';

    // protected $fillable = ['name', 'sex', 'birthday', 'email', 'emails', 'position', 'brandRepresent', 'company', 'language', 'lang', 'shop_id','region_id', 'branch_id', 'department_id','address','identification','identify_card', 'labour_end_date','working_date'];

    /**
     * To make all fields fillable.
     */
    protected $guarded = array();

    protected $hidden = ['services', 'actived_date', 'register_ip', 'visited_date', 'visited_ip', 'updated_at', 'settings',
        'services'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'visited_date',
        'birthday',
        'labour_end_date',
        'working_date',
        'identify_date',
        'passport_date',
        'passport_expiry_date',
        'finish_date',
        'from_date',
        'to_date',
        'health_last_date',
        '_updatedAt',
    ];

    public static function getPublicField()
    {
        //return $this->fillable;
    }

    // jwt implementation
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // jwt implementation
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function shift()
    {
        $shifts = Shift::orwhere(['branch_id' => mongo_id($this->branch_id)])->orWhere(['department_id' => mongo_id($this->department_id)])->orWhere(['position_id' => mongo_id($this->position_id)])->paginate();
        return $shifts;
    }

    public function shop()
    {
        $shop = Shop::where(['_id' => mongo_id($this->shop_id)])->first();
        if (!empty($shop)) {
            return $shop->transform();
        } else {
            return [];
        }
    }

    public function branch()
    {
        $branch = Branch::where(['_id' => mongo_id($this->branch_id)])->first();
        if (!empty($branch)) {
            return $branch->transform();
        } else {
            return [];
        }
    }

    public function department()
    {
        $department = Department::where(['_id' => mongo_id($this->department_id)])->first();
        if (!empty($department)) {
            return $department->transform();
        } else {
            return [];
        }
    }

    public function position()
    {
        $position = Position::where(['_id' => mongo_id($this->position_id)])->first();
        if (!empty($position)) {
            return $position->transform();
        } else {
            return [];
        }
    }

    public function transform()
    {
        $transformer = new UserTransformer();

        return $transformer->transform($this);
    }
}