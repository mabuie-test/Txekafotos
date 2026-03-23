<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Services\AuditService;

class AdminAuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('admin/auth/login', [], 'layouts/admin-guest');
    }

    public function login(): void
    {
        $email = $this->request->string('email');
        $password = (string) $this->request->input('password');

        if (!Auth::attempt($email, $password)) {
            $this->redirectWithFlash('/admin/login', ['error' => 'Credenciais inválidas.']);
        }

        $user = Auth::user();
        (new AuditService())->log('admin', (int) $user['id'], 'admin.login', 'admin', (int) $user['id'], 'Administrador autenticado no painel.');
        $this->redirectWithFlash('/admin', ['success' => 'Sessão iniciada com sucesso.']);
    }

    public function logout(): void
    {
        $user = Auth::user();
        if ($user) {
            (new AuditService())->log('admin', (int) $user['id'], 'admin.logout', 'admin', (int) $user['id'], 'Administrador encerrou a sessão.');
        }
        Auth::logout();
        $this->redirectWithFlash('/admin/login', ['success' => 'Sessão encerrada com sucesso.']);
    }
}
