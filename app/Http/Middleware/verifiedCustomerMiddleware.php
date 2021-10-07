<?php

namespace App\Http\Middleware;

use Botble\Base\Enums\BaseStatusEnum;
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
      if(auth('customer')->user() && auth('customer')->user()->status != BaseStatusEnum::ACTIVE){
        return redirect()->route('customer.pendingNotification');
      }

        if(!auth('customer')->user()->taxCertificate){
            return redirect()->route('customer.contract-form');
        }
        return $next($request);
    }
}
