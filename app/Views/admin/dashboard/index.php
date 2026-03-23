<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Total de pedidos</div><h3><?= e((string) ($overview['total_orders'] ?? 0)) ?></h3><div class="small text-muted">Sem revisão: <?= e((string) ($overview['without_revision'] ?? 0)) ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Receita total</div><h3><?= e(number_format($revenueTotal, 2)) ?> MZN</h3><div class="small text-muted">Hoje: <?= e(number_format($revenueToday, 2)) ?> MZN</div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Pedidos em produção</div><h3><?= e((string) ((int) ($overview['editing_orders'] ?? 0) + (int) ($overview['revision_orders'] ?? 0) + (int) ($overview['completed_orders'] ?? 0))) ?></h3><div class="small text-muted">Em revisão: <?= e((string) ($overview['revision_orders'] ?? 0)) ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Satisfação</div><h3><?= e(number_format((float) $averageRating, 1)) ?>/5</h3><div class="small text-muted">Feedbacks publicados: <?= e((string) ($feedbackSummary['published_feedbacks'] ?? 0)) ?></div></div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Radar operacional</h2>
                    <a href="/admin/pedidos" class="btn btn-outline-dark btn-sm">Ver fila completa</a>
                </div>
                <div class="row g-3">
                    <?php foreach ($statusStats as $stat): ?>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-light h-100">
                                <div class="small text-muted text-uppercase"><?= e(str_replace('_', ' ', $stat['status'])) ?></div>
                                <div class="fs-4 fw-bold"><?= e((string) $stat['total']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-4"><div class="rounded-4 border p-3"><div class="small text-muted">Com revisão</div><div class="fs-5 fw-semibold"><?= e((string) ($overview['with_revision'] ?? 0)) ?></div></div></div>
                    <div class="col-md-4"><div class="rounded-4 border p-3"><div class="small text-muted">Showcases activos</div><div class="fs-5 fw-semibold"><?= e((string) ($showcaseSummary['active_showcases'] ?? 0)) ?></div></div></div>
                    <div class="col-md-4"><div class="rounded-4 border p-3"><div class="small text-muted">Transações concluídas</div><div class="fs-5 fw-semibold"><?= e((string) ($financeSummary['completed_transactions'] ?? 0)) ?></div></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Serviços mais pedidos</h2>
                <div class="d-grid gap-3">
                    <?php foreach (array_slice($serviceBreakdown, 0, 6) as $row): ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><?= e(ucwords(str_replace('_', ' ', $row['service_type']))) ?></span>
                            <span class="badge text-bg-dark"><?= e((string) $row['total']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h2 class="h5">Últimos pedidos</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Pedido</th><th>Cliente</th><th>Status</th><th></th></tr></thead><tbody><?php foreach ($latestOrders as $order): ?><tr><td><div class="fw-semibold">#<?= e((string) $order['id']) ?></div><div class="small text-muted"><?= e($order['tracking_code']) ?></div></td><td><?= e($order['client_name']) ?></td><td><span class="badge text-bg-secondary"><?= e($order['status']) ?></span></td><td><a href="/admin/pedidos/<?= e((string) $order['id']) ?>" class="btn btn-outline-dark btn-sm">Abrir</a></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h2 class="h5">Últimas transações</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Ref.</th><th>Gateway</th><th>Valor</th><th>Status</th></tr></thead><tbody><?php foreach ($latestTransactions as $tx): ?><tr><td><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="text-muted small">Pedido #<?= e((string) $tx['order_id']) ?></div></td><td><?= e($tx['gateway_status'] ?? 'n/d') ?></td><td><?= e(number_format((float) $tx['amount'], 2)) ?> MZN</td><td><span class="badge text-bg-secondary"><?= e($tx['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h2 class="h5">Auditoria recente</h2><div class="d-grid gap-3"><?php foreach ($activityLogs as $log): ?><div class="border rounded-4 p-3"><div class="fw-semibold small"><?= e($log['action']) ?></div><div class="text-muted small"><?= e($log['description']) ?></div><div class="text-muted small"><?= e($log['created_at']) ?></div></div><?php endforeach; ?></div></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h2 class="h5">Indicadores rápidos</h2><ul class="list-group list-group-flush"><li class="list-group-item d-flex justify-content-between"><span>Receita do mês</span><strong><?= e(number_format($revenueMonth, 2)) ?> MZN</strong></li><li class="list-group-item d-flex justify-content-between"><span>Pendentes de pagamento</span><strong><?= e((string) ($overview['pending_orders'] ?? 0)) ?></strong></li><li class="list-group-item d-flex justify-content-between"><span>Feedbacks totais</span><strong><?= e((string) ($feedbackSummary['total_feedbacks'] ?? 0)) ?></strong></li><li class="list-group-item d-flex justify-content-between"><span>Revisões pendentes</span><strong><?= e((string) ($revisionSummary['pending_revisions'] ?? 0)) ?></strong></li><li class="list-group-item d-flex justify-content-between"><span>Ticket médio</span><strong><?= e(number_format((float) ($financeSummary['average_ticket'] ?? 0), 2)) ?> MZN</strong></li></ul></div></div></div>
</div>
