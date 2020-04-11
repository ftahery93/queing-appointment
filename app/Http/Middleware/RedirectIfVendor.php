<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Cookie;
use DB;


class RedirectIfVendor {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'vendor') {

        if (!Auth::guard($guard)->check()) {
            return redirect(config('global.fitflowVendor'));
        } elseif (!Auth::guard($guard)->user()) {
            return redirect(config('global.fitflowVendor'));
        } elseif ($request->code == '') {
            return redirect(config('global.fitflowVendor'));
        }
        elseif(DB::table('vendors')->where('id',Auth::guard($guard)->user()->vendor_id)->where('contract_enddate', '<', date("Y-m-d"))->count()==1) {
             return redirect(config('global.fitflowVendor').'/errors/noAccess');
        }
        return $next($request);
    }

}
