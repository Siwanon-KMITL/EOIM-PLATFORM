<?php

namespace App\Core;

class Router
{
    protected static array $routes = [];

    public static function get(string $uri, array|callable $action, array $middlewares = []): void
    {
        self::addRoute('GET', $uri, $action, $middlewares);
    }

    public static function post(string $uri, array|callable $action, array $middlewares = []): void
    {
        self::addRoute('POST', $uri, $action, $middlewares);
    }

    public static function put(string $uri, array|callable $action, array $middlewares = []): void
    {
        self::addRoute('PUT', $uri, $action, $middlewares);
    }

    public static function delete(string $uri, array|callable $action, array $middlewares = []): void
    {
        self::addRoute('DELETE', $uri, $action, $middlewares);
    }

    protected static function addRoute(string $method, string $uri, array|callable $action, array $middlewares = []): void
    {
        self::$routes[$method][$uri] = [
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $route = self::$routes[$method][$uri] ?? null;

        if (!$route) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        foreach ($route['middlewares'] as $middleware) {
            if (is_array($middleware)) {
                $class = $middleware[0];
                $params = $middleware[1] ?? [];
                $class::handle($params);
            } else {
                $middleware::handle();
            }
        }

        $action = $route['action'];

        if (is_callable($action)) {
            call_user_func($action);
            return;
        }

        if (is_array($action)) {
            [$controller, $controllerMethod] = $action;
            $instance = new $controller();
            call_user_func([$instance, $controllerMethod]);
            return;
        }

        http_response_code(500);
        echo "Invalid route action";
    }
}