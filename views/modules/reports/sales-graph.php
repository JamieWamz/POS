<?php

$requestedStart = isset($_GET['initialDate']) ? (string) $_GET['initialDate'] : null;
$requestedEnd = isset($_GET['finalDate']) ? (string) $_GET['finalDate'] : null;
$initialDate = valid_date($requestedStart);
$finalDate = valid_date($requestedEnd);

if (!$initialDate || !$finalDate || $initialDate > $finalDate) {
    $initialDate = date('Y-m-01');
    $finalDate = date('Y-m-d');
}

$sales = ControllerSales::ctrSalesDatesRange($initialDate, $finalDate);
$dailySales = [];
foreach ($sales as $sale) {
    $date = substr((string) $sale['saledate'], 0, 10);
    $dailySales[$date] = ($dailySales[$date] ?? 0) + (float) $sale['totalPrice'];
}
ksort($dailySales);

$chartData = [];
foreach ($dailySales as $date => $total) {
    $chartData[] = ['y' => $date, 'sales' => round($total, 2)];
}
if ($chartData === []) {
    $chartData[] = ['y' => $finalDate, 'sales' => 0];
}
?>

<div class="box box-solid bg-red-gradient">
  <div class="box-header">
    <i class="fa fa-line-chart"></i>
    <h3 class="box-title">Daily sales</h3>
  </div>
  <div class="box-body border-radius-none newSalesGraph">
    <div class="chart" id="line-chart-Sales" style="height: 250px;"></div>
  </div>
</div>

<script>
new Morris.Line({
  element: 'line-chart-Sales',
  resize: true,
  data: <?php echo json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
  xkey: 'y',
  ykeys: ['sales'],
  labels: ['Sales'],
  lineColors: ['#ffffff'],
  lineWidth: 2,
  hideHover: 'auto',
  gridTextColor: '#ffffff',
  gridStrokeWidth: 0.4,
  pointSize: 4,
  pointStrokeColors: ['#ffffff'],
  gridLineColor: 'rgba(255,255,255,.35)',
  preUnits: 'K ',
  gridTextSize: 10,
  xLabels: 'day',
  dateFormat: function (timestamp) { return new Date(timestamp).toDateString(); }
});
</script>
