<?php

namespace VCComponent\Laravel\Contact\Providers;

use Illuminate\Support\ServiceProvider;
use VCComponent\Laravel\Contact\Repositories\ContactRepository;
use VCComponent\Laravel\Contact\Repositories\ContactRepositoryEloquent;

class ContactServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

        $this->publishes([
            __DIR__ . '/../../config/contact.php' => config_path('contact.php'),
        ], 'config');
    }

    /**
     * Register any package services
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ContactRepository::class, ContactRepositoryEloquent::class);
    }
}
