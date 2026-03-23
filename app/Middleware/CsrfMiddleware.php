<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware
{
    public function handle(Request $request, Response $response): void
    {
        if (!$request->isPost()) {
            return;
        }

        if (!Csrf::validate((string) $request->input('_token'))) {
            $response->setStatus(419);
            exit('Token CSRF inválido. Atualize a página e tente novamente.');
        }
    }
}
