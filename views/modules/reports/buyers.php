<?php

$sales = ControllerSales::ctrSalesDatesRange($initialDate ?? null, $finalDate ?? null);
$customers = ControllerCustomers::ctrShowCustomers(null, null) ?: [];
$customerNames = [];
foreach ($customers as $customer) {
    $customerNames[(int) $customer['id']] = (string) $customer['name'];
}

$customerTotals = [];
foreach ($sales as $sale) {
    $name = $customerNames[(int) $sale['idCustomer']] ?? 'Former customer';
    $customerTotals[$name] = ($customerTotals[$name] ?? 0) + (float) $sale['netPrice'];
}
arsort($customerTotals);
$chartData = [];
foreach (array_slice($customerTotals, 0, 10, true) as $name => $total) {
    $chartData[] = ['y' => $name, 'a' => round($total, 2)];
}
?>

<div class="box box-default">
  <div class="box-header with-border"><h3 class="box-title">Top customers</h3></div>
  <div class="box-body">
    <?php if ($chartData === []): ?>
      <div class="pos-inline-empty">No customer sales in this period.</div>
    <?php else: ?>
      <div class="chart-responsive"><div class="chart" id="bar-chart2" style="height: 300px;"></div></div>
    <?php endif; ?>
  </div>
</div>

<?php if ($chartData !== []): ?>
<script>
new Morris.Bar({
  element: 'bar-chart2',
  resize: true,
  data: <?php echo json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
  barColors: ['#f59e0b'],
  xkey: 'y',
  ykeys: ['a'],
  labels: ['Sales'],
  preUnits: 'K ',
  hideHover: 'auto'
});
</script>
<?php endif; ?>
