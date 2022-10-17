<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $path = 'App\\Repositories\\';
        $args = [
            'BaseRepositoryInterface'       => 'BaseRepository',
            'UserRepositoryInterface'       => 'UserRepository',
            'CurrencyConversionInterface'   => 'CurrencyConversionRepository',
            'TransactionInterface'          => 'TransactionRepository',
        ];
        foreach ($args as $key => $value) {
            $this->app->bind($path . 'Interfaces\\' . $key, $path . $value);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
