<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;
use Throwable;

class App
{
    private static ?self $instance = null;
    private array $config = [];
    private Router $router;
    private Request $request;
    private Response $response;
    private Session $session;

    public function __construct(private readonly string $basePath)
    {
        define('BASE_PATH', $basePath);
        self::$instance = $this;
    }

    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    public function bootstrap(): void
    {
        $this->loadEnvironment();
        $this->loadConfiguration();
        date_default_timezone_set((string) $this->config('app.timezone', 'UTC'));

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session((string) $this->config('app.session_name', 'txekafotos_session'));
        $this->session->start();
        $this->router = new Router($this->request, $this->response);

        $this->bindCoreServices();
        require base_path('routes/web.php');
        require base_path('routes/admin.php');
    }

    public function run(): void
    {
        try {
            $this->router->dispatch();
        } catch (Throwable $exception) {
            if ((bool) $this->config('app.debug', false)) {
                http_response_code(500);
                echo '<pre>' . e($exception->getMessage() . "
" . $exception->getTraceAsString()) . '</pre>';
                return;
            }

            http_response_code(500);
            echo 'Ocorreu um erro interno. Tente novamente mais tarde.';
        }
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function session(): Session
    {
        return $this->session;
    }

    private function loadEnvironment(): void
    {
        $envFile = $this->basePath . '/.env';
        if (!file_exists($envFile)) {
            return;
        }

        if (class_exists(Dotenv::class)) {
            Dotenv::createImmutable($this->basePath)->safeLoad();
            return;
        }

        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = array_map('trim', explode('=', $line, 2));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    private function loadConfiguration(): void
    {
        foreach (glob($this->basePath . '/config/*.php') as $file) {
            $this->config[basename($file, '.php')] = require $file;
        }
    }

    private function bindCoreServices(): void
    {
        Database::initialize($this->config('database'));
        View::share('appName', (string) $this->config('app.name'));
        View::share('csrfToken', Csrf::token());
        View::share('session', $this->session);
    }
}
