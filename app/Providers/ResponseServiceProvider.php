<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\ResponseFactory;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register any response services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any response services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('jsonError', function ($code, $error) use ($factory) {
            $responseFormat = [
                'error' => [
                    'code' => $code,
                    'message' => $error,
                ]
            ];
            return $factory->json($responseFormat, $code);
        });

        $factory->macro('jsonHealthCheck', function () use ($factory) {
            return $factory->json([
                'app_name' => config('app.name'),
                'app_version' => config('app.version'),
            ]);
        });
    }
}