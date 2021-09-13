<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CheckUuidMiddleware
{
    /**
     * Filters request with invalid uuid in path parameters
     *
     * @param Request   $request
     * @param  Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Str::isUuid($request->route('uuid'))) {
            return response()->jsonError(Response::HTTP_BAD_REQUEST, 'Invalid id supplied.');
        }

        return $next($request);
    }
}