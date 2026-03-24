<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <span class="eyebrow text-primary">Financeiro</span>
        <h2 class="h3 mb-1">Receita, conversão e transações</h2>
        <p class="text-muted mb-0">Acompanhe receita, transações, conversão e exportações operacionais.</p>
    </div>
    <a href="/admin/financeiro/exportar<?= $filters['status'] ? '?status=' . urlencode($filters['status']) : '' ?>" class="btn btn-outline-primary"><i class="fa-solid fa-file-csv me-2"></i>Exportar CSV</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-coins"></i></span><div class="text-muted small">Receita total</div><div class="metric-value"><?= e(number_format($revenueTotal, 2)) ?></div><div class="small text-muted mt-2">MZN</div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-sun"></i></span><div class="text-muted small">Receita hoje</div><div class="metric-value"><?= e(number_format($revenueToday, 2)) ?></div><div class="small text-muted mt-2">MZN</div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-calendar-days"></i></span><div class="text-muted small">Receita do mês</div><div class="metric-value"><?= e(number_format($revenueMonth, 2)) ?></div><div class="small text-muted mt-2">MZN</div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-money-check-dollar"></i></span><div class="text-muted small">Ticket médio</div><div class="metric-value"><?= e(number_format((float) ($summary['average_ticket'] ?? 0), 2)) ?></div><div class="small text-muted mt-2">MZN</div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-circle-check"></i></span><div class="text-muted small">Transações concluídas</div><div class="metric-value"><?= e((string) ($summary['completed_transactions'] ?? 0)) ?></div></div></div>
    <div class="col-md-4"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></span><div class="text-muted small">Pendentes</div><div class="metric-value"><?= e((string) ($summary['pending_transactions'] ?? 0)) ?></div></div></div>
    <div class="col-md-4"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></span><div class="text-muted small">Falhadas</div><div class="metric-value"><?= e((string) ($summary['failed_transactions'] ?? 0)) ?></div></div></div>
</div>

<div class="admin-card mb-4">
    <form class="row g-3">
        <div class="col-md-3"><label class="form-label fw-semibold">Pesquisar</label><input type="text" name="q" class="form-control" value="<?= e($filters['q']) ?>" placeholder="Ref, tracking, cliente"></div>
        <div class="col-md-2"><label class="form-label fw-semibold">Status</label><select name="status" class="form-select"><option value="">Todos</option><?php foreach (['pending','processing','completed','failed','cancelled'] as $status): ?><option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><label class="form-label fw-semibold">De</label><input type="date" name="start_date" class="form-control" value="<?= e($filters['start_date']) ?>"></div>
        <div class="col-md-2"><label class="form-label fw-semibold">Até</label><input type="date" name="end_date" class="form-control" value="<?= e($filters['end_date']) ?>"></div>
        <div class="col-md-3 d-grid"><label class="form-label">&nbsp;</label><button class="btn btn-primary"><i class="fa-solid fa-filter me-2"></i>Filtrar</button></div>
    </form>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="table-card p-0 overflow-hidden">
            <div class="p-4 pb-0"><h3 class="h5 mb-3"><i class="fa-solid fa-money-bill-transfer me-2 text-primary"></i>Histórico de transações</h3></div>
            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle">
                    <thead><tr><th>Ref.</th><th>Pedido</th><th>Cliente</th><th>Gateway</th><th>Valor</th><th>Status</th></tr></thead>
                    <tbody><?php foreach ($transactions as $tx): ?><tr><td><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="text-muted small"><?= e($tx['created_at']) ?></div></td><td>#<?= e((string) $tx['order_id']) ?><div class="text-muted small"><?= e($tx['tracking_code']) ?></div></td><td><?= e($tx['client_name']) ?></td><td><?= e($tx['gateway_status'] ?? 'n/d') ?></td><td><?= e(number_format((float) $tx['amount'], 2)) ?> MZN</td><td><span class="badge <?= e(status_badge_class((string) $tx['status'])) ?> rounded-pill"><i class="<?= e(status_icon_class((string) $tx['status'])) ?> me-1"></i><?= e($tx['status']) ?></span></td></tr><?php endforeach; ?></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <h3 class="h5 mb-3"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Logs financeiros recentes</h3>
            <div class="d-grid gap-3"><?php foreach ($financialLogs as $log): ?><div class="detail-item"><div class="fw-semibold small"><?= e($log['type']) ?> · <?= e(number_format((float) $log['amount'], 2)) ?> MZN</div><div class="small text-muted"><?= e($log['description']) ?></div><div class="small text-muted mt-1"><?= e($log['created_at']) ?></div></div><?php endforeach; ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6"><div class="admin-card"><h3 class="h5 mb-3"><i class="fa-solid fa-hourglass-half me-2 text-primary"></i>Pendentes</h3><div class="d-grid gap-3"><?php foreach ($pendingTransactions as $tx): ?><div class="detail-item"><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="small text-muted">Pedido <?= e($tx['tracking_code']) ?> · <?= e($tx['client_name']) ?></div></div><?php endforeach; ?></div></div></div>
    <div class="col-lg-6"><div class="admin-card"><h3 class="h5 mb-3"><i class="fa-solid fa-circle-xmark me-2 text-primary"></i>Falhadas</h3><div class="d-grid gap-3"><?php foreach ($failedTransactions as $tx): ?><div class="detail-item"><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="small text-muted"><?= e($tx['failure_reason'] ?? 'Sem detalhe') ?></div></div><?php endforeach; ?></div></div></div>
</div>
