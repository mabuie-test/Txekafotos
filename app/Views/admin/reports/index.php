<div class="row g-4 mb-4">
    <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Taxa de satisfação</div><h3><?= e(number_format($satisfactionRate, 2)) ?>%</h3></div></div></div>
    <div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Taxa de conversão de pagamento</div><h3><?= e(number_format($paymentConversionRate, 2)) ?>%</h3></div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Pedidos por status</h2><table class="table"><tbody><?php foreach ($ordersByStatus as $row): ?><tr><td><?= e($row['status']) ?></td><td><?= e((string) $row['total']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Serviços mais solicitados</h2><table class="table"><tbody><?php foreach ($mostRequestedServices as $row): ?><tr><td><?= e($row['service_type']) ?></td><td><?= e((string) $row['total']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Pedidos por período</h2><table class="table"><tbody><?php foreach ($ordersByPeriod as $row): ?><tr><td><?= e($row['period']) ?></td><td><?= e((string) $row['total']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Feedback por período</h2><table class="table"><tbody><?php foreach ($feedbackByPeriod as $row): ?><tr><td><?= e($row['period']) ?></td><td><?= e((string) $row['total']) ?></td><td><?= e(number_format((float) $row['average_rating'],1)) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
