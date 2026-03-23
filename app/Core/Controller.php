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

    protected function redirect(string $path): void
    {
        $this->response->redirect($path);
    }
}
