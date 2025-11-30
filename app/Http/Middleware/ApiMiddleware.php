<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}