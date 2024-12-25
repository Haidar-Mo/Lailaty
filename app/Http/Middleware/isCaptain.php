<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isCaptain
{

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !in_array(Auth::user()->getRoleNames()[0], ['freeDriver', 'employeeDriver'])) {
            return response()->json([
                'message' => '! هذا الاجراء غير مصرح به ',
            ], 403);
        }

        return $next($request);
    }

}
