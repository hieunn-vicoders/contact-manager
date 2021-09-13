<?php

namespace VCComponent\Laravel\Contact\Providers;

use Illuminate\Support\ServiceProvider;
use VCComponent\Laravel\Contact\Contacts\Contact;
use VCComponent\Laravel\Contact\Contacts\Contracts\Contact as ContractsContact;
use VCComponent\Laravel\Contact\Contracts\ContactPolicyInterface;
use VCComponent\Laravel\Contact\Policies\ContactPolicy;
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

        $this->app->singleton('moduleContact.contact', function () {
            return new Contact();
        });

        $this->app->bind(ContractsContact::class, 'moduleContact.contact');
        $this->app->bind(ContactPolicyInterface::class, ContactPolicy::class);
        $this->app->register(ContactAuthServiceProvider::class);
    }
    public function provides()
    {
        return [
            ContractsContact::class,
            'moduleProduct.product',
        ];
    }
}
