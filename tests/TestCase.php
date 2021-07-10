<?php

namespace VCComponent\Laravel\Contact\Test;

use Dingo\Api\Provider\LaravelServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use VCComponent\Laravel\Contact\Providers\ContactServiceProvider;
use VCComponent\Laravel\User\Providers\UserComponentEventProvider;
use VCComponent\Laravel\User\Providers\UserComponentProvider;
use VCComponent\Laravel\User\Providers\UserComponentRouteProvider;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return VCComponent\Laravel\Generator\Providers\GeneratorServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [
            ContactServiceProvider::class,
            LaravelServiceProvider::class,
            \Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
            \Illuminate\Auth\AuthServiceProvider::class,
            UserComponentEventProvider::class,
            UserComponentProvider::class,
            UserComponentRouteProvider::class,
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/../src/database/factories');
        $this->withFactories(__DIR__ . '/../tests/Stubs/Factory');

    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:TEQ1o2POo+3dUuWXamjwGSBx/fsso+viCCg9iFaXNUA=');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('contact.namespace', 'contact-management');
        $app['config']->set('contact.models', [
            'contact' => \VCComponent\Laravel\Contact\Entities\Contact::class,
        ]);
        $app['config']->set('contact.transformers', [
            'contact' => \VCComponent\Laravel\Contact\Transformers\ContactTransformer::class,
        ]);

        $app['config']->set('contact.auth_middleware', [
            'admin' => [],
            'frontend' => [],
        ]);
        $app['config']->set('contact.test_mode', true);
        $app['config']->set('jwt.secret', '5jMwJkcDTUKlzcxEpdBRIbNIeJt1q5kmKWxa0QA2vlUEG6DRlxcgD7uErg51kbBl');
        $app['config']->set('auth.providers.users.model', \VCComponent\Laravel\User\Entities\User::class);
        $app['config']->set('user', ['namespace' => 'user-management']);
        $app['config']->set('api', [
            'standardsTree' => 'x',
            'subtype' => '',
            'version' => 'v1',
            'prefix' => 'api',
            'domain' => null,
            'name' => null,
            'conditionalRequest' => true,
            'strict' => false,
            'debug' => true,
            'errorFormat' => [
                'message' => ':message',
                'errors' => ':errors',
                'code' => ':code',
                'status_code' => ':status_code',
                'debug' => ':debug',
            ],
            'middleware' => [
            ],
            'auth' => [
            ],
            'throttling' => [
            ],
            'transformer' => \Dingo\Api\Transformer\Adapter\Fractal::class,
            'defaultFormat' => 'json',
            'formats' => [
                'json' => \Dingo\Api\Http\Response\Format\Json::class,
            ],
            'formatsOptions' => [
                'json' => [
                    'pretty_print' => false,
                    'indent_style' => 'space',
                    'indent_size' => 2,
                ],
            ],
        ]);
    }

    public function assertValidation($response, $field, $error_message)
    {
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            "errors" => [
                $field => [
                    $error_message,
                ],
            ],
        ]);
    }
}
