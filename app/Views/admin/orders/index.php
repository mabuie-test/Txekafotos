<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <span class="eyebrow text-primary">Pedidos</span>
        <h2 class="h3 mb-1">Gestão completa de pedidos</h2>
        <p class="text-muted mb-0">Filtre a operação por status, datas, serviço ou pesquisa livre.</p>
    </div>
    <a href="/admin/revisoes" class="btn btn-outline-primary"><i class="fa-solid fa-rotate me-2"></i>Ver revisões</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-box"></i></span><div class="text-muted small">Total</div><div class="metric-value"><?= e((string) ($overview['total_orders'] ?? 0)) ?></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-regular fa-clock"></i></span><div class="text-muted small">Pendentes</div><div class="metric-value"><?= e((string) ($overview['pending_orders'] ?? 0)) ?></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-gear"></i></span><div class="text-muted small">Em edição</div><div class="metric-value"><?= e((string) ($overview['editing_orders'] ?? 0)) ?></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-circle-check"></i></span><div class="text-muted small">Aprovados</div><div class="metric-value"><?= e((string) ($overview['approved_orders'] ?? 0)) ?></div></div></div>
</div>

<div class="admin-card mb-4">
    <form class="row g-3">
        <div class="col-md-3"><label class="form-label fw-semibold">Pesquisar</label><input type="text" name="q" class="form-control" value="<?= e($filters['q']) ?>" placeholder="Nome, telefone, tracking..."></div>
        <div class="col-md-2"><label class="form-label fw-semibold">Status</label><select name="status" class="form-select"><option value="">Todos</option><?php foreach (\App\Models\Order::STATUSES as $item): ?><option value="<?= e($item) ?>" <?= $filters['status'] === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label fw-semibold">Serviço</label><select name="service_type" class="form-select"><option value="">Todos</option><?php foreach ($serviceBreakdown as $item): ?><option value="<?= e($item['service_type']) ?>" <?= $filters['service_type'] === $item['service_type'] ? 'selected' : '' ?>><?= e($item['service_type']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label fw-semibold">De</label><input type="date" name="start_date" class="form-control" value="<?= e($filters['start_date']) ?>"></div>
        <div class="col-md-2"><label class="form-label fw-semibold">Até</label><input type="date" name="end_date" class="form-control" value="<?= e($filters['end_date']) ?>"></div>
        <div class="col-md-1 d-grid"><label class="form-label">&nbsp;</label><button class="btn btn-primary"><i class="fa-solid fa-filter"></i></button></div>
    </form>
</div>

<div class="table-card p-0 overflow-hidden">
    <div class="table-responsive px-3 py-3">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Pedido</th><th>Cliente</th><th>Serviço</th><th>Status</th><th>Valor</th><th>Revisões</th><th>Criado</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><div class="fw-semibold">#<?= e((string) $order['id']) ?></div><div class="small text-muted"><?= e($order['tracking_code']) ?></div></td>
                        <td><div><?= e($order['client_name']) ?></div><div class="small text-muted"><?= e($order['client_phone']) ?></div></td>
                        <td><?= e($order['service_type'] ?: 'Personalizado') ?></td>
                        <td><span class="badge <?= e(status_badge_class((string) $order['status'])) ?> rounded-pill"><i class="<?= e(status_icon_class((string) $order['status'])) ?> me-1"></i><?= e($order['status']) ?></span></td>
                        <td><?= e(number_format((float) $order['amount'], 2)) ?> MZN</td>
                        <td><?= e((string) $order['revisions_used']) ?></td>
                        <td><?= e($order['created_at']) ?></td>
                        <td><a href="/admin/pedidos/<?= e((string) $order['id']) ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-eye me-2"></i>Detalhes</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
