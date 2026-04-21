<?php

if (!function_exists('app_log')) {
    function app_log(string $message): void
    {
        $directory = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $file = $directory . '/app.log';
        $time = date('Y-m-d H:i:s');
        file_put_contents($file, "[{$time}] {$message}" . PHP_EOL, FILE_APPEND);
    }
}
