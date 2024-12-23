<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MustBeFullRegistered
{

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->full_registered==true) {
            return response()->json([
                'message' => '! يرجى اكمال تسجيل حسابك ',
            ], 403);
        }

        return $next($request);
    }

}
