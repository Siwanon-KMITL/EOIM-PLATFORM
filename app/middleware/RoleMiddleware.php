<?php

namespace App\Middleware;

class RoleMiddleware
{
    public static function handle(array|string $roles): void
    {
        if (!has_role($roles)) {
            http_response_code(403);
            exit('403 Forbidden');
        }
    }
}