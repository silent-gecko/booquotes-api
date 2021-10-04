<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
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
                    'message' => $error ?: Response::$statusTexts[$code],
                ]
            ];
            return $factory->json($responseFormat, $code);
        });

        $factory->macro('jsonValidationError', function (ValidationException $exception) use ($factory) {
            $responseFormat = [
                'error' => [
                    'code' => $exception->status,
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                ]
            ];
            return $factory->json($responseFormat, $exception->status);
        });

        $factory->macro('jsonCreated', function ($entityId) use ($factory) {
            $responseFormat = [
                'data' => [
                    'id' => $entityId,
                ]
            ];
            return $factory->json($responseFormat, Response::HTTP_CREATED);
        });

        $factory->macro('jsonHealthCheck', function () use ($factory) {
            return $factory->json([
                'app_name' => config('app.name'),
                'app_version' => config('app.version'),
            ]);
        });
    }
}