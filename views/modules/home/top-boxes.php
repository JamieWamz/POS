<?php
$today = date('Y-m-d');
$salesToday = ControllerSales::ctrSalesDatesRange($today, $today);
$expensesToday = ControllerExpenses::ctrExpensesDatesRange($today, $today);
$totalSalesToday = array_sum(array_map(static fn ($sale) => (float) $sale['totalPrice'], $salesToday ?: []));
$totalExpensesToday = array_sum(array_map(static fn ($expense) => (float) $expense['amount'], $expensesToday ?: []));
$netToday = $totalSalesToday - $totalExpensesToday;
$products = ControllerProducts::ctrShowProducts(null, null, 'id') ?: [];
$lowStock = array_filter($products, static fn ($product) => (int) $product['stock'] <= 10);
$metrics = [
  ['value' => 'K' . number_format($totalSalesToday, 2), 'label' => 'Gross sales today', 'icon' => 'fa-shopping-bag', 'href' => 'sales', 'tone' => 'positive'],
  ['value' => ($netToday < 0 ? '-K' : 'K') . number_format(abs($netToday), 2), 'label' => 'Net after expenses', 'icon' => 'fa-line-chart', 'href' => 'reports', 'tone' => $netToday < 0 ? 'negative' : 'positive'],
  ['value' => number_format(count($salesToday ?: [])), 'label' => 'Transactions today', 'icon' => 'fa-exchange', 'href' => 'sales', 'tone' => 'neutral'],
  ['value' => number_format(count($lowStock)), 'label' => 'Low-stock products', 'icon' => 'fa-exclamation-triangle', 'href' => 'products', 'tone' => count($lowStock) > 0 ? 'warning' : 'positive'],
];
?>
<?php foreach ($metrics as $metric): ?>
  <div class="col-lg-3 col-sm-6 col-xs-12">
    <a class="pos-metric-card pos-metric-card--<?php echo e($metric['tone']); ?>" href="<?php echo e($metric['href']); ?>">
      <div>
        <span><?php echo e($metric['label']); ?></span>
        <strong><?php echo e($metric['value']); ?></strong>
      </div>
      <i class="fa <?php echo e($metric['icon']); ?>" aria-hidden="true"></i>
    </a>
  </div>
<?php endforeach; ?>
