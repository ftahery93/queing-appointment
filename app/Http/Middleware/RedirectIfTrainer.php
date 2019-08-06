<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use DB;

class RedirectIfTrainer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'trainer')
    {
    
        if (!Auth::guard($guard)->check()) {
            return redirect('trainer');
        }
        elseif(!Auth::guard($guard)->user()){
             return redirect('trainer');
        }
         elseif(DB::table('trainers')->where('id',Auth::guard($guard)->user()->id)->where('contract_enddate', '<', date("Y-m-d"))->count()==1) {
             return redirect('trainer/errors/noAccessTrainer');
        }
        return $next($request);
    }
}
