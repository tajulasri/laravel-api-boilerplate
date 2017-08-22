<?php

use Dingo\Api\Auth\Auth;

if (!function_exists('get_authenticate_user')) {

    function get_authenticate_user()
    {
        return app()->make(Auth::class)->authenticate();
    }
}
