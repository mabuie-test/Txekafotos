<?php $title = 'Criar pedido'; ?>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h2 mb-1">Enviar foto para edição</h1>
                        <p class="text-muted mb-0">Descreva com detalhes como quer reconstruir, restaurar ou montar a sua memória.</p>
                    </div>
                    <span class="badge bg-warning text-dark fs-6">45 MZN</span>
                </div>
                <?php if (!empty($flash)): ?><div class="alert alert-success"><?= e($flash) ?></div><?php endif; ?>
                <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $fieldErrors): foreach ($fieldErrors as $fieldError): ?><li><?= e($fieldError) ?></li><?php endforeach; endforeach; ?></ul></div><?php endif; ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-4 mb-4">
                            <div class="col-md-4"><div class="small rounded-4 bg-light p-3 h-100"><strong>1. Envie as imagens</strong><div class="text-muted mt-1">Foto principal obrigatória e até <?= e((string) $maxExtraImages) ?> anexos adicionais.</div></div></div>
                            <div class="col-md-4"><div class="small rounded-4 bg-light p-3 h-100"><strong>2. Descreva o pedido</strong><div class="text-muted mt-1">Explique exatamente quem deve entrar, sair, mudar de lado ou ser restaurado.</div></div></div>
                            <div class="col-md-4"><div class="small rounded-4 bg-light p-3 h-100"><strong>3. Pague via M-Pesa</strong><div class="text-muted mt-1">Após criar o pedido, a cobrança é iniciada pelo número informado.</div></div></div>
                        </div>
                        <form method="post" action="/pedido" enctype="multipart/form-data" class="row g-4 needs-validation" novalidate>
                            <?= \App\Core\Csrf::field() ?>
                            <div class="col-md-6">
                                <label class="form-label">Nome completo</label>
                                <input type="text" name="client_name" class="form-control" value="<?= e($old['client_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefone M-Pesa</label>
                                <input type="text" name="client_phone" class="form-control" placeholder="25884XXXXXXX" value="<?= e($old['client_phone'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tipo de serviço</label>
                                <select name="service_type" class="form-select">
                                    <option value="">Selecione, se desejar</option>
                                    <?php foreach ($serviceTypes as $serviceType): ?>
                                        <option value="<?= e($serviceType) ?>" <?= ($old['service_type'] ?? '') === $serviceType ? 'selected' : '' ?>><?= e(ucwords(str_replace('_', ' ', $serviceType))) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descrição detalhada</label>
                                <textarea name="description" rows="6" class="form-control" placeholder="Ex.: Quero inserir meu pai na foto, usar a segunda foto como referência, remover riscos e melhorar as cores..." required><?= e($old['description'] ?? '') ?></textarea>
                                <small class="text-muted">Quanto mais detalhes der, melhor será a execução manual da equipa.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto principal</label>
                                <input type="file" name="primary_image" class="form-control image-input" data-preview-target="#primaryPreview" accept=".jpg,.jpeg,.png,.webp" required>
                                <small class="text-muted">Obrigatória. Formatos: JPG, JPEG, PNG ou WEBP. Até <?= e((string) $maxUploadMb) ?> MB.</small>
                                <div id="primaryPreview" class="upload-preview mt-3"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fotos adicionais</label>
                                <input type="file" name="extra_images[]" multiple class="form-control image-input" data-preview-target="#extraPreview" data-counter-target="#extraImagesCount" accept=".jpg,.jpeg,.png,.webp">
                                <small class="text-muted">Máximo de <?= e((string) $maxExtraImages) ?> imagens adicionais, cada uma com até <?= e((string) $maxUploadMb) ?> MB.</small>
                                <div class="small mt-2">Selecionadas: <strong id="extraImagesCount">0</strong></div>
                                <div id="extraPreview" class="upload-preview mt-3 d-flex gap-2 flex-wrap"></div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms" <?= !empty($old['terms']) ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="terms">Confirmo que aceito os termos do serviço e autorizo o processamento das imagens enviadas.</label>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <p class="text-muted small mb-0">Depois de criar o pedido, receberá um ID para acompanhar o progresso e confirmar o pagamento.</p>
                                <button class="btn btn-dark btn-lg">Criar pedido</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
