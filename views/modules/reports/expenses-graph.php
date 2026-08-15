<?php

$requestedStart = isset($_GET['initialDate']) ? (string) $_GET['initialDate'] : null;
$requestedEnd = isset($_GET['finalDate']) ? (string) $_GET['finalDate'] : null;
$initialDate = valid_date($requestedStart);
$finalDate = valid_date($requestedEnd);
if (($requestedStart !== null || $requestedEnd !== null) && (!$initialDate || !$finalDate || $initialDate > $finalDate)) {
    $initialDate = null;
    $finalDate = null;
}

$expenses = ControllerExpenses::ctrExpensesDatesRange($initialDate, $finalDate);
$monthlyExpenses = [];
foreach ($expenses as $expense) {
    $month = substr((string) $expense['date'], 0, 7);
    $monthlyExpenses[$month] = ($monthlyExpenses[$month] ?? 0) + (float) $expense['amount'];
}
ksort($monthlyExpenses);

$chartData = [];
foreach ($monthlyExpenses as $month => $total) {
    $chartData[] = ['y' => $month, 'expenses' => round($total, 2)];
}
if ($chartData === []) {
    $chartData[] = ['y' => date('Y-m'), 'expenses' => 0];
}
?>

<div class="box box-solid bg-red-gradient">
  <div class="box-header">
    <i class="fa fa-line-chart"></i>
    <h3 class="box-title">Monthly expenses</h3>
  </div>
  <div class="box-body border-radius-none">
    <div class="chart" id="line-chart-expenses" style="height: 250px;"></div>
  </div>
</div>

<script>
new Morris.Line({
  element: 'line-chart-expenses',
  resize: true,
  data: <?php echo json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
  xkey: 'y',
  ykeys: ['expenses'],
  labels: ['Expenses'],
  lineColors: ['#ffffff'],
  lineWidth: 2,
  hideHover: 'auto',
  gridTextColor: '#ffffff',
  gridStrokeWidth: 0.4,
  pointSize: 4,
  pointStrokeColors: ['#ffffff'],
  gridLineColor: 'rgba(255,255,255,.35)',
  preUnits: 'K ',
  gridTextSize: 10
});
</script>
