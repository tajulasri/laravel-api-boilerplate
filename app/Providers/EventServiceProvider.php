<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        app('Dingo\Api\Exception\Handler')->register(function (ModelNotFoundException $exception) {
            return \Response::make(['status' => 'error', 'message' => $exception->getMessage()], 404);
        });

        app('Dingo\Api\Exception\Handler')->register(function (ValidationException $exception) {
            return \Response::make(['status' => 'error', 'message' => 'Validation failed!', 'errors' => $exception->validator->getMessageBag()->toArray()], 400);
        });

        app('Dingo\Api\Exception\Handler')->register(function (JWTException $exception) {
            return \Response::make(['status' => 'error', 'message' => $exception->getMessage()], 401);
        });
    }
}
