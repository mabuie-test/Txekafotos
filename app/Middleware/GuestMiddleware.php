<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class GuestMiddleware
{
    public function handle(Request $request, Response $response): void
    {
        if (Auth::check()) {
            $response->redirect('/admin');
        }
    }
}
