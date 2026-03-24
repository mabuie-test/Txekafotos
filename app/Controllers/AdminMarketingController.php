<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Services\AuditService;
use App\Services\HomepageService;

class AdminMarketingController extends Controller
{
    public function index(): void
    {
        $this->view('admin/marketing/index', (new HomepageService())->getHomepageData(), 'layouts/admin');
    }

    public function updateHomepage(): void
    {
        (new HomepageService())->updateHomepage([
            'hero_title' => $this->request->string('hero_title'),
            'hero_subtitle' => $this->request->string('hero_subtitle'),
            'hero_cta_text' => $this->request->string('hero_cta_text'),
            'hero_secondary_cta_text' => $this->request->string('hero_secondary_cta_text'),
            'hero_badge_text' => $this->request->string('hero_badge_text'),
            'section_benefits_title' => $this->request->string('section_benefits_title'),
            'section_feedback_title' => $this->request->string('section_feedback_title'),
            'section_showcase_title' => $this->request->string('section_showcase_title'),
            'final_cta_title' => $this->request->string('final_cta_title'),
            'final_cta_text' => $this->request->string('final_cta_text'),
            'benefits_text' => $this->request->string('benefits_text'),
            'stats' => [
                'clientes_satisfeitos' => $this->request->integer('clientes_satisfeitos'),
                'avaliacao_media' => (float) $this->request->input('avaliacao_media'),
                'pedidos_entregues' => $this->request->integer('pedidos_entregues'),
            ],
        ]);
        (new AuditService())->log('admin', (int) (Auth::user()['id'] ?? 0), 'marketing.homepage_updated', 'homepage_content', 1, 'Conteúdo da homepage atualizado.');
        $this->redirectWithFlash('/admin/marketing', ['success' => 'Conteúdo da homepage atualizado com sucesso.']);
    }
}
