<?php

if (!function_exists('base_path_url')) {
    function base_path_url(string $path = ''): string
    {
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base = rtrim($scriptName, '/');

        if ($base === '/' || $base === '\\') {
            $base = '';
        }

        $path = ltrim($path, '/');

        return $base . ($path ? '/' . $path : '');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . base_path_url($path));
        exit;
    }
}