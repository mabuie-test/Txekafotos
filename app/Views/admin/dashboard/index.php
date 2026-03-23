<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <span class="eyebrow text-primary">Dashboard</span>
        <h2 class="h3 mb-1">Visão geral da operação</h2>
        <p class="text-muted mb-0">Monitorize pedidos, satisfação, finanças e produção numa interface mais clara.</p>
    </div>
    <a href="/admin/pedidos" class="btn btn-primary"><i class="fa-solid fa-list me-2"></i>Ver fila completa</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-box"></i></span><div class="text-muted small">Total de pedidos</div><div class="metric-value"><?= e((string) ($overview['total_orders'] ?? 0)) ?></div><div class="small text-muted mt-2">Sem revisão: <?= e((string) ($overview['without_revision'] ?? 0)) ?></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-coins"></i></span><div class="text-muted small">Receita total</div><div class="metric-value"><?= e(number_format($revenueTotal, 2)) ?></div><div class="small text-muted mt-2">Hoje: <?= e(number_format($revenueToday, 2)) ?> MZN</div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-users"></i></span><div class="text-muted small">Pedidos em produção</div><div class="metric-value"><?= e((string) ((int) ($overview['editing_orders'] ?? 0) + (int) ($overview['revision_orders'] ?? 0) + (int) ($overview['completed_orders'] ?? 0))) ?></div><div class="small text-muted mt-2">Em revisão: <?= e((string) ($overview['revision_orders'] ?? 0)) ?></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="admin-stat-card"><span class="stat-icon"><i class="fa-solid fa-star"></i></span><div class="text-muted small">Satisfação</div><div class="metric-value"><?= e(number_format((float) $averageRating, 1)) ?></div><div class="small text-muted mt-2">Feedbacks publicados: <?= e((string) ($feedbackSummary['published_feedbacks'] ?? 0)) ?></div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="admin-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 mb-0"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Radar operacional</h3>
                <span class="badge-soft"><i class="fa-solid fa-bolt"></i>Atualização diária</span>
            </div>
            <div class="row g-3 mb-4">
                <?php foreach ($statusStats as $stat): ?>
                    <div class="col-md-4">
                        <div class="detail-item h-100">
                            <div class="small text-muted text-uppercase"><?= e(str_replace('_', ' ', $stat['status'])) ?></div>
                            <div class="metric-value mt-2"><?= e((string) $stat['total']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="row g-3">
                <div class="col-md-4"><div class="detail-item"><div class="small text-muted">Com revisão</div><strong><?= e((string) ($overview['with_revision'] ?? 0)) ?></strong></div></div>
                <div class="col-md-4"><div class="detail-item"><div class="small text-muted">Showcases activos</div><strong><?= e((string) ($showcaseSummary['active_showcases'] ?? 0)) ?></strong></div></div>
                <div class="col-md-4"><div class="detail-item"><div class="small text-muted">Transações concluídas</div><strong><?= e((string) ($financeSummary['completed_transactions'] ?? 0)) ?></strong></div></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card h-100">
            <h3 class="h5 mb-3"><i class="fa-solid fa-layer-group me-2 text-primary"></i>Serviços mais pedidos</h3>
            <div class="d-grid gap-3">
                <?php foreach (array_slice($serviceBreakdown, 0, 6) as $row): ?>
                    <div class="detail-item d-flex justify-content-between align-items-center">
                        <span><?= e(ucwords(str_replace('_', ' ', $row['service_type']))) ?></span>
                        <span class="badge text-bg-primary rounded-pill"><?= e((string) $row['total']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="table-card p-0 overflow-hidden">
            <div class="p-4 pb-0"><h3 class="h5 mb-3"><i class="fa-solid fa-box-open me-2 text-primary"></i>Últimos pedidos</h3></div>
            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle">
                    <thead><tr><th>Pedido</th><th>Cliente</th><th>Status</th><th>Ação</th></tr></thead>
                    <tbody>
                    <?php foreach ($latestOrders as $order): ?>
                        <tr>
                            <td><div class="fw-semibold">#<?= e((string) $order['id']) ?></div><div class="small text-muted"><?= e($order['tracking_code']) ?></div></td>
                            <td><?= e($order['client_name']) ?></td>
                            <td><span class="badge <?= e(status_badge_class((string) $order['status'])) ?> rounded-pill"><i class="<?= e(status_icon_class((string) $order['status'])) ?> me-1"></i><?= e($order['status']) ?></span></td>
                            <td><a href="/admin/pedidos/<?= e((string) $order['id']) ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-eye me-2"></i>Abrir</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-card p-0 overflow-hidden">
            <div class="p-4 pb-0"><h3 class="h5 mb-3"><i class="fa-solid fa-money-bill-wave me-2 text-primary"></i>Últimas transações</h3></div>
            <div class="table-responsive px-3 pb-3">
                <table class="table align-middle">
                    <thead><tr><th>Ref.</th><th>Gateway</th><th>Valor</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($latestTransactions as $tx): ?>
                        <tr>
                            <td><div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div><div class="text-muted small">Pedido #<?= e((string) $tx['order_id']) ?></div></td>
                            <td><?= e($tx['gateway_status'] ?? 'n/d') ?></td>
                            <td><?= e(number_format((float) $tx['amount'], 2)) ?> MZN</td>
                            <td><span class="badge <?= e(status_badge_class((string) $tx['status'])) ?> rounded-pill"><i class="<?= e(status_icon_class((string) $tx['status'])) ?> me-1"></i><?= e($tx['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <h3 class="h5 mb-3"><i class="fa-solid fa-file-waveform me-2 text-primary"></i>Auditoria recente</h3>
            <div class="d-grid gap-3">
                <?php foreach ($activityLogs as $log): ?>
                    <div class="detail-item">
                        <div class="fw-semibold small"><?= e($log['action']) ?></div>
                        <div class="text-muted small"><?= e($log['description']) ?></div>
                        <div class="text-muted small mt-1"><?= e($log['created_at']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <h3 class="h5 mb-3"><i class="fa-solid fa-gauge-high me-2 text-primary"></i>Indicadores rápidos</h3>
            <div class="d-grid gap-3">
                <div class="detail-item d-flex justify-content-between"><span>Receita do mês</span><strong><?= e(number_format($revenueMonth, 2)) ?> MZN</strong></div>
                <div class="detail-item d-flex justify-content-between"><span>Pendentes de pagamento</span><strong><?= e((string) ($overview['pending_orders'] ?? 0)) ?></strong></div>
                <div class="detail-item d-flex justify-content-between"><span>Feedbacks totais</span><strong><?= e((string) ($feedbackSummary['total_feedbacks'] ?? 0)) ?></strong></div>
                <div class="detail-item d-flex justify-content-between"><span>Revisões pendentes</span><strong><?= e((string) ($revisionSummary['pending_revisions'] ?? 0)) ?></strong></div>
                <div class="detail-item d-flex justify-content-between"><span>Ticket médio</span><strong><?= e(number_format((float) ($financeSummary['average_ticket'] ?? 0), 2)) ?> MZN</strong></div>
            </div>
        </div>
    </div>
</div>
