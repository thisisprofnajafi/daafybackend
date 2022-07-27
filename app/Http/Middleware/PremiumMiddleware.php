<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\RespondHandler;

class PremiumMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        if(auth()->user()->premium && auth()->user()->premium->ends >= Carbon::now()){

            return $next($request);

        }else{
            auth()->user()->is_premium = false;
            auth()->user()->premium()->delete();
            auth()->user()->save();
            return RespondHandler::respond(['middle'=>true,'type'=>'premium'],200);

        }

    }
}
