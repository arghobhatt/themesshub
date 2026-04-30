<?php
declare(strict_types=1);

namespace Core;

final class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    public function get(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): self
    {
        return $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, callable|array $handler, array $middleware = []): self
    {
        $method = strtoupper($method);
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];

        return $this;
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $method = strtoupper($requestMethod);

        $route = $this->routes[$method][$path] ?? null;
        if ($route === null) {
            http_response_code(404);
            $viewPath = __DIR__ . '/../app/Views/errors/404.php';
            if (is_file($viewPath)) {
                require $viewPath;
                return;
            }
            echo '404 Not Found';
            return;
        }

        if (is_array($route) && array_key_exists('handler', $route)) {
            $handler = $route['handler'];
            $middleware = $route['middleware'] ?? [];
        } else {
            $handler = $route;
            $middleware = [];
        }

        foreach ($middleware as $guard) {
            if (!is_callable($guard)) {
                continue;
            }

            $result = call_user_func($guard);
            if ($result === false) {
                return;
            }
        }
        if (is_callable($handler)) {
            call_user_func($handler);
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;

            if (class_exists($class) && method_exists($class, $method)) {
                $controller = new $class();
                $controller->$method();
                return;
            }
        }

        http_response_code(500);
        echo '500 Route handler error';
    }
}
