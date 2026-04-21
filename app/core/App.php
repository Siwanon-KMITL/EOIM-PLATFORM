<?php

namespace App\Core;

class App
{
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        require_once __DIR__ . '/../../routes/web.php';
        require_once __DIR__ . '/../../routes/api.php';
    }

    public function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($scriptName !== '/' && str_starts_with($uri, $scriptName)) {
            $uri = substr($uri, strlen($scriptName));
        }

        $uri = $uri ?: '/';

        $this->router->dispatch($method, $uri);
    }
}