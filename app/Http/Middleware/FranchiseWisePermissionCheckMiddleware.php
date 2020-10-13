<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class FranchiseWisePermissionCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $is_all, ...$permissions)
    {
        if (is_array($permissions)) {
            $role = Auth::user()->singleRoleOfFranchise();
            $isGiven = false;
            if ($is_all=='true') {
                $isGiven = $role->hasAllPermissions($permissions);
            } else {
                $isGiven = $role->hasAnyPermission($permissions);
            }
            if ($isGiven) {
                return $next($request);
            }
        }
        return json_response([], 'You Don\'n have access to this resource.', 403);

    }
}
