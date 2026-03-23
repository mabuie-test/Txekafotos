<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h2 class="h4 mb-1">Financeiro</h2><p class="text-muted mb-0">Acompanhe receita, transações, conversão e exportações operacionais.</p></div>
    <a href="/admin/financeiro/exportar<?= $filters['status'] ? '?status=' . urlencode($filters['status']) : '' ?>" class="btn btn-outline-dark">Exportar CSV</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Receita total</div><h3><?= e(number_format($revenueTotal, 2)) ?> MZN</h3></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Receita hoje</div><h3><?= e(number_format($revenueToday, 2)) ?> MZN</h3></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Receita do mês</div><h3><?= e(number_format($revenueMonth, 2)) ?> MZN</h3></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Ticket médio</div><h3><?= e(number_format((float) ($summary['average_ticket'] ?? 0), 2)) ?> MZN</h3></div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Transações concluídas</div><div class="fs-4 fw-bold"><?= e((string) ($summary['completed_transactions'] ?? 0)) ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Pendentes</div><div class="fs-4 fw-bold"><?= e((string) ($summary['pending_transactions'] ?? 0)) ?></div></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Falhadas</div><div class="fs-4 fw-bold"><?= e((string) ($summary['failed_transactions'] ?? 0)) ?></div></div></div></div>
</div>

<div class="card border-0 shadow-sm mb-4"><div class="card-body p-4"><form class="row g-3"><div class="col-md-3"><label class="form-label">Pesquisar</label><input type="text" name="q" class="form-control" value="<?= e($filters['q']) ?>" placeholder="Ref, tracking, cliente"></div><div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Todos</option><?php foreach (['pending','processing','completed','failed','cancelled'] as $status): ?><option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select></div><div class="col-md-2"><label class="form-label">De</label><input type="date" name="start_date" class="form-control" value="<?= e($filters['start_date']) ?>"></div><div class="col-md-2"><label class="form-label">Até</label><input type="date" name="end_date" class="form-control" value="<?= e($filters['end_date']) ?>"></div><div class="col-md-3 d-grid"><label class="form-label">&nbsp;</label><button class="btn btn-dark">Filtrar</button></div></form></div></div>

<div class="row g-4 mb-4">
    <div class="col-lg-8"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h3 class="h5 mb-3">Histórico de transações</h3><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ref.</th><th>Pedido</th><th>Cliente</th><th>Gateway</th><th>Valor</th><th>Status</th></tr></thead><tbody><?php foreach ($transactions as $tx): ?><tr><td><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="text-muted small"><?= e($tx['created_at']) ?></div></td><td>#<?= e((string) $tx['order_id']) ?><div class="text-muted small"><?= e($tx['tracking_code']) ?></div></td><td><?= e($tx['client_name']) ?></td><td><?= e($tx['gateway_status'] ?? 'n/d') ?></td><td><?= e(number_format((float) $tx['amount'], 2)) ?> MZN</td><td><span class="badge text-bg-secondary"><?= e($tx['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
    <div class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h3 class="h5 mb-3">Logs financeiros recentes</h3><div class="d-grid gap-3"><?php foreach ($financialLogs as $log): ?><div class="border rounded-4 p-3"><div class="fw-semibold small"><?= e($log['type']) ?> · <?= e(number_format((float) $log['amount'], 2)) ?> MZN</div><div class="small text-muted"><?= e($log['description']) ?></div><div class="small text-muted"><?= e($log['created_at']) ?></div></div><?php endforeach; ?></div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h3 class="h5 mb-3">Pendentes</h3><div class="d-grid gap-3"><?php foreach ($pendingTransactions as $tx): ?><div class="border rounded-4 p-3"><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="small text-muted">Pedido <?= e($tx['tracking_code']) ?> · <?= e($tx['client_name']) ?></div></div><?php endforeach; ?></div></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h3 class="h5 mb-3">Falhadas</h3><div class="d-grid gap-3"><?php foreach ($failedTransactions as $tx): ?><div class="border rounded-4 p-3"><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="small text-muted"><?= e($tx['failure_reason'] ?? 'Sem detalhe') ?></div></div><?php endforeach; ?></div></div></div></div>
</div>
