<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'GET, POST, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '0',
            'Access-Control-Allow-Headers'     =>
                'Accept, Content-Type, Authorization, X-Requested-With, ' . config('app.api_key_header_name'),
//            'Access-Control-Expose-Headers'   => '',
        ];

        if ($request->isMethod('OPTIONS')) {
            return response('', Response::HTTP_NO_CONTENT, $headers);
        }

        return $next($request)->withHeaders($headers);
    }
}