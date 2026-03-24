<?php $title = 'Transforme fotos em memórias eternas'; ?>
<section class="hero-section section-shell text-white">
    <div class="hero-orb"></div>
    <div class="container py-lg-4">
        <div class="row align-items-center g-4 g-xl-5">
            <div class="col-lg-7">
                <span class="eyebrow text-warning mb-3"><?= e($content['hero_badge_text'] ?? 'Pagamento seguro') ?></span>
                <h1 class="display-4 fw-bold mb-3"><i class="fa-solid fa-camera text-warning me-2"></i><?= e($content['hero_title'] ?? '') ?></h1>
                <p class="lead text-white-50 mb-4"><?= e($content['hero_subtitle'] ?? '') ?></p>
                <div class="action-group mb-4">
                    <a href="/pedido/criar" class="btn btn-warning btn-lg px-4"><i class="fa-solid fa-image me-2"></i><?= e($content['hero_cta_text'] ?? 'Enviar foto') ?></a>
                    <a href="/acompanhar" class="btn btn-outline-light btn-lg px-4"><i class="fa-solid fa-clock-rotate-left me-2"></i><?= e($content['hero_secondary_cta_text'] ?? 'Acompanhar') ?></a>
                </div>
                <div class="row g-3">
                    <div class="col-sm-6 col-xl-3"><div class="glass-panel h-100"><div class="small text-white-50">Clientes</div><div class="metric-value"><?= e((string) ($stats['clientes_satisfeitos'] ?? 0)) ?>+</div></div></div>
                    <div class="col-sm-6 col-xl-3"><div class="glass-panel h-100"><div class="small text-white-50">Avaliação</div><div class="metric-value"><?= e((string) ($stats['avaliacao_media'] ?? $average_rating)) ?></div></div></div>
                    <div class="col-sm-6 col-xl-3"><div class="glass-panel h-100"><div class="small text-white-50">Pedidos</div><div class="metric-value"><?= e((string) ($stats['pedidos_entregues'] ?? 0)) ?>+</div></div></div>
                    <div class="col-sm-6 col-xl-3"><div class="glass-panel h-100"><div class="small text-white-50">Entrega</div><div class="metric-value">24/7</div></div></div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-cta-card text-white h-100">
                    <span class="eyebrow text-warning mb-3">Processo simples</span>
                    <h2 class="h3 mb-4">Da foto original ao resultado final em poucos passos.</h2>
                    <div class="d-grid gap-3">
                        <div class="d-flex gap-3 align-items-start"><span class="step-icon bg-white text-primary"><i class="fa-solid fa-upload"></i></span><div><strong>Envie a foto</strong><div class="text-white-50 small">Foto principal obrigatória e anexos adicionais como referência.</div></div></div>
                        <div class="d-flex gap-3 align-items-start"><span class="step-icon bg-white text-primary"><i class="fa-solid fa-credit-card"></i></span><div><strong>Pague com segurança</strong><div class="text-white-50 small">Cobrança direta via M-Pesa com verificação do estado da transação.</div></div></div>
                        <div class="d-flex gap-3 align-items-start"><span class="step-icon bg-white text-primary"><i class="fa-solid fa-pen-ruler"></i></span><div><strong>Edição manual</strong><div class="text-white-50 small">Equipe especializada restaura, monta e melhora a sua memória.</div></div></div>
                        <div class="d-flex gap-3 align-items-start"><span class="step-icon bg-white text-primary"><i class="fa-solid fa-download"></i></span><div><strong>Receba e aprove</strong><div class="text-white-50 small">Baixe o resultado final ou peça revisão quando necessário.</div></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="beneficios" class="section-shell bg-white">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow text-primary">Benefícios</span>
            <h2 class="mt-2 mb-3"><?= e($content['section_benefits_title'] ?? 'Porque escolher Txekafotos') ?></h2>
            <p class="text-muted mb-0"><?= e($content['benefits_text'] ?? '') ?></p>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3"><article class="feature-card"><span class="feature-icon mb-3"><i class="fa-solid fa-bolt"></i></span><h3 class="h5">Fluxo rápido</h3><p class="text-muted mb-0">Pedido simples, acompanhamento online e comunicação clara em cada etapa.</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="feature-card"><span class="feature-icon mb-3"><i class="fa-solid fa-shield-halved"></i></span><h3 class="h5">Pagamento seguro</h3><p class="text-muted mb-0">Integração com M-Pesa e confirmação de transação antes da produção.</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="feature-card"><span class="feature-icon mb-3"><i class="fa-solid fa-star"></i></span><h3 class="h5">Qualidade profissional</h3><p class="text-muted mb-0">Tratamento cuidadoso para restauração, montagem e melhoria visual.</p></article></div>
            <div class="col-md-6 col-xl-3"><article class="feature-card"><span class="feature-icon mb-3"><i class="fa-solid fa-users"></i></span><h3 class="h5">Confiança do cliente</h3><p class="text-muted mb-0">Experiência clara para clientes novos e recorrentes em qualquer dispositivo.</p></article></div>
        </div>
        <?php if (!empty($banners)): ?>
            <div class="row g-4">
                <?php foreach ($banners as $banner): ?>
                    <div class="col-lg-6">
                        <article class="feature-card h-100">
                            <span class="eyebrow text-primary mb-2">Campanha</span>
                            <h3 class="h4 mb-2"><?= e($banner['title']) ?></h3>
                            <p class="text-muted mb-4"><?= e($banner['subtitle']) ?></p>
                            <a href="<?= e($banner['button_link'] ?: '/pedido/criar') ?>" class="btn btn-outline-primary"><i class="fa-solid fa-bullhorn me-2"></i><?= e($banner['button_text'] ?: 'Saiba mais') ?></a>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="como-funciona" class="section-shell">
    <div class="container">
        <div class="section-heading text-center mx-auto">
            <span class="eyebrow text-primary">Como funciona</span>
            <h2 class="mt-2 mb-3">Um processo claro do upload até à entrega final.</h2>
            <p class="text-muted mb-0">Criámos uma jornada intuitiva para o cliente saber exatamente o que fazer e o que esperar em cada fase.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-xl-3"><div class="metric-card"><span class="step-icon mb-3"><i class="fa-solid fa-upload"></i></span><h3 class="h5">1. Upload</h3><p class="text-muted mb-0">Envie a imagem principal e referências adicionais com descrição detalhada.</p></div></div>
            <div class="col-md-6 col-xl-3"><div class="metric-card"><span class="step-icon mb-3"><i class="fa-solid fa-credit-card"></i></span><h3 class="h5">2. Pagamento</h3><p class="text-muted mb-0">Inicie a cobrança via M-Pesa e confirme o pagamento com poucos cliques.</p></div></div>
            <div class="col-md-6 col-xl-3"><div class="metric-card"><span class="step-icon mb-3"><i class="fa-solid fa-edit"></i></span><h3 class="h5">3. Edição</h3><p class="text-muted mb-0">O pedido entra na fila da equipa para tratamento profissional da imagem.</p></div></div>
            <div class="col-md-6 col-xl-3"><div class="metric-card"><span class="step-icon mb-3"><i class="fa-solid fa-download"></i></span><h3 class="h5">4. Entrega</h3><p class="text-muted mb-0">Acompanhe o status, aprove o resultado e baixe a imagem final.</p></div></div>
        </div>
    </div>
</section>

<section id="showcases" class="section-shell bg-white">
    <div class="container">
        <div class="section-heading d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
            <div>
                <span class="eyebrow text-primary">Antes e depois</span>
                <h2 class="mt-2 mb-3"><?= e($content['section_showcase_title'] ?? '') ?></h2>
                <p class="text-muted mb-0">Veja como imagens antigas, danificadas ou incompletas ganham nova vida.</p>
            </div>
            <span class="badge-soft"><i class="fa-solid fa-arrows-left-right"></i> Transformação visual</span>
        </div>
        <div class="row g-4">
            <?php foreach ($showcases as $showcase): ?>
                <div class="col-lg-4">
                    <article class="showcase-card">
                        <div class="showcase-split mb-3">
                            <img src="<?= e(media_url($showcase['before_image'])) ?>" class="showcase-img" alt="Antes">
                            <img src="<?= e(media_url($showcase['after_image'])) ?>" class="showcase-img" alt="Depois">
                            <span class="showcase-badge"><i class="fa-solid fa-arrows-left-right"></i></span>
                        </div>
                        <h3 class="h5"><?= e($showcase['title']) ?></h3>
                        <p class="text-muted mb-0"><?= e($showcase['description']) ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="container">
        <div class="section-heading text-center mx-auto">
            <span class="eyebrow text-primary">Feedback</span>
            <h2 class="mt-2 mb-3"><?= e($content['section_feedback_title'] ?? '') ?></h2>
            <p class="text-muted mb-0">A prova social é parte importante da experiência e da confiança no serviço.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="col-lg-4">
                    <article class="feedback-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="feature-icon"><i class="fa-solid fa-quote-left"></i></span>
                            <div class="rating-stars"><?php for ($i = 0; $i < (int) $feedback['rating']; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?></div>
                        </div>
                        <p class="mb-4"><?= e($feedback['message']) ?></p>
                        <div class="d-flex align-items-center gap-3">
                            <span class="feature-icon"><i class="fa-solid fa-user-circle"></i></span>
                            <div>
                                <strong class="d-block"><?= e($feedback['client_name']) ?></strong>
                                <small class="text-muted">Cliente satisfeito</small>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-shell bg-dark text-white">
    <div class="container">
        <div class="surface-card bg-transparent border border-white border-opacity-10 text-center text-md-start px-4 px-lg-5 py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="eyebrow text-warning">CTA final</span>
                    <h2 class="display-6 fw-bold mt-2 mb-3"><?= e($content['final_cta_title'] ?? '') ?></h2>
                    <p class="text-white-50 mb-0"><?= e($content['final_cta_text'] ?? '') ?></p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="/pedido/criar" class="btn btn-warning btn-lg px-4"><i class="fa-solid fa-arrow-right me-2"></i>Enviar foto agora</a>
                </div>
            </div>
        </div>
    </div>
</section>
