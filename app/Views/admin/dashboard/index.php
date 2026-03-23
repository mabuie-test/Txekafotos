<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted">Receita total</span><h3><?= e(number_format($revenueTotal, 2)) ?> MZN</h3></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted">Receita do dia</span><h3><?= e(number_format($revenueToday, 2)) ?> MZN</h3></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><span class="text-muted">Receita do mês</span><h3><?= e(number_format($revenueMonth, 2)) ?> MZN</h3></div></div></div>
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><h2 class="h5">Pedidos por status</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody><?php foreach ($statusStats as $stat): ?><tr><td><?= e($stat['status']) ?></td><td><?= e((string) $stat['total']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body"><h2 class="h5">Indicadores</h2><ul class="list-group list-group-flush"><li class="list-group-item">Média de avaliação: <strong><?= e((string) $averageRating) ?></strong></li><li class="list-group-item">Showcases activos: <strong><?= e((string) $showcaseCount) ?></strong></li><li class="list-group-item">Pedidos recentes: <strong><?= e((string) count($latestOrders)) ?></strong></li></ul></div></div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Últimos pedidos</h2><div class="table-responsive"><table class="table"><thead><tr><th>ID</th><th>Cliente</th><th>Status</th></tr></thead><tbody><?php foreach ($latestOrders as $order): ?><tr><td><a href="/admin/pedidos/<?= e((string) $order['id']) ?>">#<?= e((string) $order['id']) ?></a></td><td><?= e($order['client_name']) ?></td><td><?= e($order['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Últimas transações</h2><div class="table-responsive"><table class="table"><thead><tr><th>Ref.</th><th>Valor</th><th>Status</th></tr></thead><tbody><?php foreach ($latestTransactions as $tx): ?><tr><td><?= e($tx['debito_reference']) ?></td><td><?= e(number_format((float) $tx['amount'],2)) ?></td><td><?= e($tx['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
</div>
