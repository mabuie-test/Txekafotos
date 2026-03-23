<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Services\AuditService;
use App\Services\HomepageService;

class AdminMarketingController extends Controller
{
    public function index(): void
    {
        $this->view('admin/marketing/index', (new HomepageService())->getHomepageData() + [
            'flash' => App::getInstance()?->session()->getFlash('message'),
        ], 'layouts/admin');
    }

    public function updateHomepage(): void
    {
        (new HomepageService())->updateHomepage([
            'hero_title' => $this->request->input('hero_title'),
            'hero_subtitle' => $this->request->input('hero_subtitle'),
            'hero_cta_text' => $this->request->input('hero_cta_text'),
            'hero_secondary_cta_text' => $this->request->input('hero_secondary_cta_text'),
            'hero_badge_text' => $this->request->input('hero_badge_text'),
            'section_benefits_title' => $this->request->input('section_benefits_title'),
            'section_feedback_title' => $this->request->input('section_feedback_title'),
            'section_showcase_title' => $this->request->input('section_showcase_title'),
            'final_cta_title' => $this->request->input('final_cta_title'),
            'final_cta_text' => $this->request->input('final_cta_text'),
            'benefits_text' => $this->request->input('benefits_text'),
            'stats' => [
                'clientes_satisfeitos' => (int) $this->request->input('clientes_satisfeitos'),
                'avaliacao_media' => (float) $this->request->input('avaliacao_media'),
                'pedidos_entregues' => (int) $this->request->input('pedidos_entregues'),
            ],
        ]);
        (new AuditService())->log('admin', (int) (Auth::user()['id'] ?? 0), 'marketing.homepage_updated', 'homepage_content', 1, 'Conteúdo da homepage atualizado.');
        App::getInstance()?->session()->flash('message', 'Conteúdo da homepage atualizado com sucesso.');
        $this->redirect('/admin/marketing');
    }
}
