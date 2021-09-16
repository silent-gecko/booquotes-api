<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
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
    public function boot()
    {
        Builder::macro('joinOnce', function(...$params) {
            $isJoined = collect($this->joins)->pluck('table')->contains($params[0]);
            return $isJoined ? $this : call_user_func_array([$this, 'join'], $params);
        });
    }
}
