<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected Request $request, protected Response $response)
    {
    }

    protected function view(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        echo View::make($view, $data, $layout);
    }

    protected function redirect(string $path, int $status = 302): void
    {
        $this->response->redirect($path, $status);
    }

    protected function session(): Session
    {
        return App::getInstance()->session();
    }

    protected function redirectWithFlash(string $path, array $flashes, int $status = 302): void
    {
        $this->session()->flashMany($flashes);
        $this->redirect($path, $status);
    }

    protected function back(string $fallback = '/', array $flashes = []): void
    {
        if ($flashes !== []) {
            $this->session()->flashMany($flashes);
        }
        $target = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($target);
    }
}
