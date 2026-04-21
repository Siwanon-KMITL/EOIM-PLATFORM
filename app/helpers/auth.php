<?php

if (!function_exists('start_session_if_not_started')) {
    function start_session_if_not_started(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): ?array
    {
        start_session_if_not_started();
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return auth_user() !== null;
    }
}

if (!function_exists('has_role')) {
    function has_role(array|string $roles): bool
    {
        $user = auth_user();

        if (!$user) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($user['role'], $roles, true);
    }
}