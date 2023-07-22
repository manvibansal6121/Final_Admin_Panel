<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPermission;
use App\Models\Permission;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permissionLevel
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissionLevel)
    {   
        //Admin with all permission access
        if (Auth::user()->profile == 1) {
            return $next($request);
        }
        //Staff with some permission access
         elseif (Auth::user()->profile == null) {
            $permission = Permission::where('permission', $permissionLevel)->first();
            $userPermission = UserPermission::where([
                'admin_id' => Auth::user()->id,
                'permission_id' => $permission->id,
            ])->first();

            if ($userPermission) {
                return $next($request);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
        }
    }
}
