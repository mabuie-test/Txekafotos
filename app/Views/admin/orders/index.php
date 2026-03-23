<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h2 class="h4 mb-1">Pedidos</h2>
        <p class="text-muted mb-0">Filtre a operação por status, datas, serviço ou pesquisa livre.</p>
    </div>
    <a href="/admin/revisoes" class="btn btn-outline-dark">Ver revisões</a>
</div>

<?php if (!empty($flash)): ?><div class="alert alert-success"><?= e($flash) ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total</div><div class="fs-4 fw-bold"><?= e((string) ($overview['total_orders'] ?? 0)) ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Pendentes</div><div class="fs-4 fw-bold"><?= e((string) ($overview['pending_orders'] ?? 0)) ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Em edição</div><div class="fs-4 fw-bold"><?= e((string) ($overview['editing_orders'] ?? 0)) ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Aprovados</div><div class="fs-4 fw-bold"><?= e((string) ($overview['approved_orders'] ?? 0)) ?></div></div></div></div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form class="row g-3">
            <div class="col-md-3"><label class="form-label">Pesquisar</label><input type="text" name="q" class="form-control" value="<?= e($filters['q']) ?>" placeholder="Nome, telefone, tracking..."></div>
            <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Todos</option><?php foreach (\App\Models\Order::STATUSES as $item): ?><option value="<?= e($item) ?>" <?= $filters['status'] === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label">Serviço</label><select name="service_type" class="form-select"><option value="">Todos</option><?php foreach ($serviceBreakdown as $item): ?><option value="<?= e($item['service_type']) ?>" <?= $filters['service_type'] === $item['service_type'] ? 'selected' : '' ?>><?= e($item['service_type']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label">De</label><input type="date" name="start_date" class="form-control" value="<?= e($filters['start_date']) ?>"></div>
            <div class="col-md-2"><label class="form-label">Até</label><input type="date" name="end_date" class="form-control" value="<?= e($filters['end_date']) ?>"></div>
            <div class="col-md-1 d-grid"><label class="form-label">&nbsp;</label><button class="btn btn-dark">Filtrar</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Pedido</th><th>Cliente</th><th>Serviço</th><th>Status</th><th>Valor</th><th>Revisões</th><th>Criado</th><th></th></tr></thead><tbody><?php foreach ($orders as $order): ?><tr><td><div class="fw-semibold">#<?= e((string) $order['id']) ?></div><div class="small text-muted"><?= e($order['tracking_code']) ?></div></td><td><div><?= e($order['client_name']) ?></div><div class="small text-muted"><?= e($order['client_phone']) ?></div></td><td><?= e($order['service_type'] ?: 'Personalizado') ?></td><td><span class="badge text-bg-secondary"><?= e($order['status']) ?></span></td><td><?= e(number_format((float) $order['amount'], 2)) ?> MZN</td><td><?= e((string) $order['revisions_used']) ?></td><td><?= e($order['created_at']) ?></td><td><a href="/admin/pedidos/<?= e((string) $order['id']) ?>" class="btn btn-outline-dark btn-sm">Detalhes</a></td></tr><?php endforeach; ?></tbody></table></div></div>
