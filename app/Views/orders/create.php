<?php $title = 'Criar pedido'; ?>
<section class="section-shell">
    <div class="container">
        <div class="row justify-content-center g-4">
            <div class="col-xl-10">
                <div class="section-heading mb-4">
                    <span class="eyebrow text-primary">Novo pedido</span>
                    <h1 class="mt-2 mb-3">Envie a sua foto com uma experiência mais clara, rápida e intuitiva.</h1>
                    <p class="text-muted mb-0">Use as referências, descreva exatamente o que precisa e acompanhe tudo até ao resultado final.</p>
                </div>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger border-0 shadow-sm rounded-4">
                        <div class="d-flex gap-3">
                            <span class="alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $fieldError): ?>
                                    <li><?= e($fieldError) ?></li>
                                <?php endforeach; endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row g-4 align-items-start">
                    <div class="col-lg-4">
                        <aside class="surface-card h-100">
                            <span class="badge text-bg-warning mb-3 fs-6"><i class="fa-solid fa-money-bill-wave me-2"></i>45 MZN</span>
                            <h2 class="h4 mb-3">O que acontece depois?</h2>
                            <div class="d-grid gap-3">
                                <div class="d-flex gap-3 align-items-start"><span class="step-icon"><i class="fa-solid fa-upload"></i></span><div><strong>Upload imediato</strong><div class="text-muted small">Foto principal obrigatória e até <?= e((string) $maxExtraImages) ?> anexos.</div></div></div>
                                <div class="d-flex gap-3 align-items-start"><span class="step-icon"><i class="fa-solid fa-phone"></i></span><div><strong>Validação M-Pesa</strong><div class="text-muted small">Use um número válido para iniciar a cobrança.</div></div></div>
                                <div class="d-flex gap-3 align-items-start"><span class="step-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span><div><strong>Edição profissional</strong><div class="text-muted small">A equipa trabalha no pedido após a confirmação do pagamento.</div></div></div>
                                <div class="d-flex gap-3 align-items-start"><span class="step-icon"><i class="fa-solid fa-download"></i></span><div><strong>Entrega digital</strong><div class="text-muted small">Receba, aprove e baixe o resultado quando estiver pronto.</div></div></div>
                            </div>
                        </aside>
                    </div>
                    <div class="col-lg-8">
                        <div class="surface-card">
                            <form method="post" action="/pedido" enctype="multipart/form-data" class="row g-4 needs-validation" novalidate>
                                <?= \App\Core\Csrf::field() ?>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome completo</label>
                                    <div class="form-icon-field">
                                        <i class="fa-solid fa-user field-icon"></i>
                                        <input type="text" name="client_name" class="form-control" value="<?= e((string) old('client_name')) ?>" placeholder="Ex.: Maria Joaquim" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telefone M-Pesa</label>
                                    <div class="form-icon-field">
                                        <i class="fa-solid fa-phone field-icon"></i>
                                        <input type="text" name="client_phone" class="form-control" placeholder="25884XXXXXXX" value="<?= e((string) old('client_phone')) ?>" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Tipo de serviço</label>
                                    <div class="form-icon-field">
                                        <i class="fa-solid fa-layer-group field-icon"></i>
                                        <select name="service_type" class="form-select">
                                            <option value="">Selecione, se desejar</option>
                                            <?php foreach ($serviceTypes as $serviceType): ?>
                                                <option value="<?= e($serviceType) ?>" <?= old('service_type') === $serviceType ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $serviceType))) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Descrição detalhada</label>
                                    <div class="form-icon-field textarea-field">
                                        <i class="fa-solid fa-pen field-icon"></i>
                                        <textarea name="description" rows="6" class="form-control" placeholder="Ex.: Quero inserir meu pai na foto, remover riscos, melhorar as cores e usar a segunda imagem como referência..." required><?= e((string) old('description')) ?></textarea>
                                    </div>
                                    <small class="text-muted">Quanto mais detalhes der, melhor será a execução manual da equipa.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Foto principal</label>
                                    <div class="upload-dropzone upload-card">
                                        <div class="d-flex gap-3 align-items-start mb-3">
                                            <span class="upload-icon"><i class="fa-solid fa-upload"></i></span>
                                            <div>
                                                <strong>Arraste e solte ou escolha um ficheiro</strong>
                                                <div class="small text-muted">Obrigatória · JPG, JPEG, PNG ou WEBP até <?= e((string) $maxUploadMb) ?> MB.</div>
                                            </div>
                                        </div>
                                        <input type="file" name="primary_image" class="form-control image-input" data-preview-target="#primaryPreview" accept=".jpg,.jpeg,.png,.webp" required>
                                        <div id="primaryPreview" class="upload-preview mt-3"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fotos adicionais</label>
                                    <div class="upload-dropzone upload-card">
                                        <div class="d-flex gap-3 align-items-start mb-3">
                                            <span class="upload-icon"><i class="fa-solid fa-images"></i></span>
                                            <div>
                                                <strong>Envie referências complementares</strong>
                                                <div class="small text-muted">Máximo de <?= e((string) $maxExtraImages) ?> imagens adicionais.</div>
                                            </div>
                                        </div>
                                        <input type="file" name="extra_images[]" multiple class="form-control image-input" data-preview-target="#extraPreview" data-counter-target="#extraImagesCount" accept=".jpg,.jpeg,.png,.webp">
                                        <div class="small mt-2 text-muted">Selecionadas: <strong id="extraImagesCount">0</strong></div>
                                        <div id="extraPreview" class="upload-preview mt-3"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check p-3 rounded-4 border bg-light-subtle">
                                        <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" <?= old('terms') ? 'checked' : '' ?> required>
                                        <label class="form-check-label ms-2" for="terms">Confirmo que aceito os termos do serviço e autorizo o processamento das imagens enviadas.</label>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <p class="text-muted small mb-0">Depois de criar o pedido, receberá um ID para acompanhar o progresso e confirmar o pagamento.</p>
                                    <button class="btn btn-primary btn-lg px-4"><i class="fa-solid fa-arrow-right me-2"></i>Criar pedido</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
