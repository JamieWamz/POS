<?php

$sales = ControllerSales::ctrSalesDatesRange($initialDate ?? null, $finalDate ?? null);
$users = ControllerUsers::ctrShowUsers(null, null) ?: [];
$userNames = [];
foreach ($users as $user) {
    $userNames[(int) $user['id']] = (string) $user['name'];
}

$sellerTotals = [];
foreach ($sales as $sale) {
    $name = $userNames[(int) $sale['idSeller']] ?? 'Former user';
    $sellerTotals[$name] = ($sellerTotals[$name] ?? 0) + (float) $sale['netPrice'];
}
arsort($sellerTotals);
$chartData = [];
foreach ($sellerTotals as $name => $total) {
    $chartData[] = ['y' => $name, 'a' => round($total, 2)];
}
?>

<div class="box box-default">
  <div class="box-header with-border"><h3 class="box-title">Sales by team member</h3></div>
  <div class="box-body">
    <?php if ($chartData === []): ?>
      <div class="pos-inline-empty">No team sales in this period.</div>
    <?php else: ?>
      <div class="chart-responsive"><div class="chart" id="bar-chart1" style="height: 300px;"></div></div>
    <?php endif; ?>
  </div>
</div>

<?php if ($chartData !== []): ?>
<script>
new Morris.Bar({
  element: 'bar-chart1',
  resize: true,
  data: <?php echo json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
  barColors: ['#2563eb'],
  xkey: 'y',
  ykeys: ['a'],
  labels: ['Sales'],
  preUnits: 'K ',
  hideHover: 'auto'
});
</script>
<?php endif; ?>
