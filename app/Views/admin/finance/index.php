<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Receita total</div><h3><?= e(number_format($revenueTotal, 2)) ?> MZN</h3></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Receita hoje</div><h3><?= e(number_format($revenueToday, 2)) ?> MZN</h3></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm"><div class="card-body"><div class="text-muted">Receita mês</div><h3><?= e(number_format($revenueMonth, 2)) ?> MZN</h3></div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Pendentes</h2><table class="table"><thead><tr><th>Ref</th><th>Pedido</th><th>Valor</th></tr></thead><tbody><?php foreach ($pendingTransactions as $tx): ?><tr><td><?= e($tx['debito_reference']) ?></td><td><?= e((string) $tx['order_id']) ?></td><td><?= e(number_format((float) $tx['amount'],2)) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    <div class="col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body"><h2 class="h5">Falhadas</h2><table class="table"><thead><tr><th>Ref</th><th>Pedido</th><th>Valor</th></tr></thead><tbody><?php foreach ($failedTransactions as $tx): ?><tr><td><?= e($tx['debito_reference']) ?></td><td><?= e((string) $tx['order_id']) ?></td><td><?= e(number_format((float) $tx['amount'],2)) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div>
<div class="card border-0 shadow-sm mt-4"><div class="card-body"><h2 class="h5">Linha do tempo financeira</h2><table class="table"><thead><tr><th>Data</th><th>Total</th></tr></thead><tbody><?php foreach ($timeline as $row): ?><tr><td><?= e($row['period']) ?></td><td><?= e(number_format((float) $row['total'],2)) ?> MZN</td></tr><?php endforeach; ?></tbody></table></div></div>
