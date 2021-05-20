<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Theme;
class verifiedCustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
      if(auth('customer')->user() && auth('customer')->user()->status != 'verified'){
        return redirect()->route('customer.pendingNotification');
      }
        return $next($request);
    }
}
