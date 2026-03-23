<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

class Router
{
    private array $routes = [];
    private array $groupStack = [];

    public function __construct(private readonly Request $request, private readonly Response $response)
    {
    }

    public function get(string $path, array|Closure $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|Closure $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function group(array $attributes, Closure $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function dispatch(): void
    {
        $method = $this->request->method();
        $path = $this->request->path();

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                foreach ($route['middleware'] as $middlewareClass) {
                    (new $middlewareClass())->handle($this->request, $this->response);
                }

                $handler = $route['handler'];
                if ($handler instanceof Closure) {
                    $handler(...array_values($params));
                    return;
                }

                [$controllerClass, $action] = $handler;
                $controller = new $controllerClass($this->request, $this->response);
                $controller->{$action}(...array_values($params));
                return;
            }
        }

        $this->response->setStatus(404);
        echo View::make('partials/404');
    }

    private function addRoute(string $method, string $path, array|Closure $handler, array $middleware = []): void
    {
        $prefix = '';
        $groupMiddleware = [];
        foreach ($this->groupStack as $group) {
            $prefix .= $group['prefix'] ?? '';
            $groupMiddleware = [...$groupMiddleware, ...($group['middleware'] ?? [])];
        }

        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/');
        $fullPath = $fullPath === '//' ? '/' : $fullPath;
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $fullPath);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => [...$groupMiddleware, ...$middleware],
        ];
    }
}
