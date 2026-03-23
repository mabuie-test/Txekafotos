<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Services\AuditService;

class AdminAuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('admin/auth/login', ['error' => App::getInstance()?->session()->getFlash('error')], 'layouts/admin-guest');
    }

    public function login(): void
    {
        $email = (string) $this->request->input('email');
        $password = (string) $this->request->input('password');

        if (!Auth::attempt($email, $password)) {
            App::getInstance()?->session()->flash('error', 'Credenciais inválidas.');
            $this->redirect('/admin/login');
        }

        $user = Auth::user();
        (new AuditService())->log('admin', (int) $user['id'], 'admin.login', 'admin', (int) $user['id'], 'Administrador autenticado no painel.');
        $this->redirect('/admin');
    }

    public function logout(): void
    {
        $user = Auth::user();
        if ($user) {
            (new AuditService())->log('admin', (int) $user['id'], 'admin.logout', 'admin', (int) $user['id'], 'Administrador encerrou a sessão.');
        }
        Auth::logout();
        $this->redirect('/admin/login');
    }
}
