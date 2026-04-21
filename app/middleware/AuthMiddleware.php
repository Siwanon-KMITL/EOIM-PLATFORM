<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!is_logged_in()) {
            redirect('/login');
        }
    }
}