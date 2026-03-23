<?php $title = 'Transforme fotos em memórias eternas'; ?>
<section class="hero-section py-5 bg-dark text-white">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark mb-3"><?= e($content['hero_badge_text'] ?? 'Pagamento seguro') ?></span>
                <h1 class="display-5 fw-bold mb-3"><?= e($content['hero_title'] ?? '') ?></h1>
                <p class="lead text-white-50 mb-4"><?= e($content['hero_subtitle'] ?? '') ?></p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/pedido/criar" class="btn btn-warning btn-lg"><?= e($content['hero_cta_text'] ?? 'Enviar foto') ?></a>
                    <a href="/acompanhar" class="btn btn-outline-light btn-lg"><?= e($content['hero_secondary_cta_text'] ?? 'Acompanhar') ?></a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h2 class="h5">Como funciona</h2>
                        <ol class="mb-0 small text-muted">
                            <li>Envie a foto principal e anexos adicionais.</li>
                            <li>Descreva exatamente a transformação desejada.</li>
                            <li>Pague 45 MZN via M-Pesa.</li>
                            <li>Acompanhe, revise e aprove o resultado final.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4"><div class="stat-card"><strong><?= e((string) ($stats['clientes_satisfeitos'] ?? 0)) ?>+</strong><span>clientes satisfeitos</span></div></div>
            <div class="col-md-4"><div class="stat-card"><strong><?= e((string) ($stats['avaliacao_media'] ?? $average_rating)) ?></strong><span>média de avaliação</span></div></div>
            <div class="col-md-4"><div class="stat-card"><strong><?= e((string) ($stats['pedidos_entregues'] ?? 0)) ?>+</strong><span>pedidos entregues</span></div></div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3"><?= e($content['section_benefits_title'] ?? '') ?></h2>
                <p class="text-muted mb-0"><?= e($content['benefits_text'] ?? '') ?></p>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($banners as $banner): ?>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h3 class="h5"><?= e($banner['title']) ?></h3>
                            <p class="text-muted"><?= e($banner['subtitle']) ?></p>
                            <a href="<?= e($banner['button_link'] ?: '/pedido/criar') ?>" class="btn btn-outline-dark"><?= e($banner['button_text'] ?: 'Saiba mais') ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="showcases" class="py-5 bg-white">
    <div class="container">
        <h2 class="h3 mb-4"><?= e($content['section_showcase_title'] ?? '') ?></h2>
        <div class="row g-4">
            <?php foreach ($showcases as $showcase): ?>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="row g-0">
                            <div class="col-6"><img src="<?= e(media_url($showcase['before_image'])) ?>" class="img-fluid rounded-start showcase-img" alt="Antes"></div>
                            <div class="col-6"><img src="<?= e(media_url($showcase['after_image'])) ?>" class="img-fluid rounded-end showcase-img" alt="Depois"></div>
                        </div>
                        <div class="card-body">
                            <h3 class="h6"><?= e($showcase['title']) ?></h3>
                            <p class="small text-muted mb-0"><?= e($showcase['description']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <h2 class="h3 mb-4"><?= e($content['section_feedback_title'] ?? '') ?></h2>
        <div class="row g-4">
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="mb-2 text-warning"><?= str_repeat('★', (int) $feedback['rating']) ?></div>
                            <p class="mb-3"><?= e($feedback['message']) ?></p>
                            <strong><?= e($feedback['client_name']) ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 bg-dark text-white">
    <div class="container text-center">
        <h2 class="h2 mb-3"><?= e($content['final_cta_title'] ?? '') ?></h2>
        <p class="text-white-50 mb-4"><?= e($content['final_cta_text'] ?? '') ?></p>
        <a href="/pedido/criar" class="btn btn-warning btn-lg">Enviar foto agora</a>
    </div>
</section>
