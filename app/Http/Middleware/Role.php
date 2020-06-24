<?php

namespace App\Http\Middleware;

use App\Api\Repositories\Contracts\UserRepository;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Role
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if(mongo_id_string($user->role_id) == '5def177d44050000e700388a') {
            echo "Không có quyền";return;
        }
        return $next($request);
    }
}
