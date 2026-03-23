<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h2 class="h4 mb-1">Relatórios</h2><p class="text-muted mb-0">Visão gerencial de operação, satisfação, conversão e serviços.</p></div>
</div>

<div class="card border-0 shadow-sm mb-4"><div class="card-body p-4"><form class="row g-3"><div class="col-md-4"><label class="form-label">De</label><input type="date" name="start_date" class="form-control" value="<?= e($filters['start_date']) ?>"></div><div class="col-md-4"><label class="form-label">Até</label><input type="date" name="end_date" class="form-control" value="<?= e($filters['end_date']) ?>"></div><div class="col-md-4 d-grid"><label class="form-label">&nbsp;</label><button class="btn btn-dark">Atualizar relatório</button></div></form></div></div>

<div class="row g-4 mb-4">
    <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Taxa de satisfação</div><h3><?= e(number_format($satisfactionRate, 2)) ?>%</h3></div></div></div>
    <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Taxa de conversão de pagamento</div><h3><?= e(number_format($paymentConversionRate, 2)) ?>%</h3></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5">Pedidos por status</h2><table class="table"><tbody><?php foreach ($ordersByStatus as $row): ?><tr><td><?= e($row['status']) ?></td><td><?= e((string) $row['total']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5">Serviços mais solicitados</h2><table class="table"><tbody><?php foreach ($mostRequestedServices as $row): ?><tr><td><?= e($row['service_type']) ?></td><td><?= e((string) $row['total']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5">Pedidos por período</h2><table class="table"><tbody><?php foreach ($ordersByPeriod as $row): ?><tr><td><?= e($row['period']) ?></td><td><?= e((string) $row['total']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5">Feedback por período</h2><table class="table"><tbody><?php foreach ($feedbackByPeriod as $row): ?><tr><td><?= e($row['period']) ?></td><td><?= e((string) $row['total']) ?></td><td><?= e(number_format((float) $row['average_rating'], 1)) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5">Pedidos com revisão</h2><table class="table"><thead><tr><th>Pedido</th><th>Cliente</th><th>Qtd.</th></tr></thead><tbody><?php foreach ($ordersWithRevisions as $row): ?><tr><td>#<?= e((string) $row['id']) ?></td><td><?= e($row['client_name']) ?></td><td><?= e((string) $row['revision_count']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5">Aprovados sem revisão</h2><table class="table"><thead><tr><th>Pedido</th><th>Cliente</th><th>Aprovado em</th></tr></thead><tbody><?php foreach ($approvedWithoutRevision as $row): ?><tr><td>#<?= e((string) $row['id']) ?></td><td><?= e($row['client_name']) ?></td><td><?= e($row['approved_at'] ?? 'n/d') ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
