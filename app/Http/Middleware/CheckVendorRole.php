<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Vendor\User;
use Auth;


class CheckVendorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
   public function handle($request, Closure $next, $permission)
    {
	
        if (!Auth::guard('vendor')->user()->hasRolePermission($permission)) {
		 return redirect(config('global.fitflowVendor'). config('global.storeAddress').'/errors/402');
        }
		return $next($request);
		
        
    }
}
