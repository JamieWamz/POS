<?php

$products = ControllerProducts::ctrShowProducts(null, null, 'sales') ?: [];
$topProducts = array_slice($products, 0, 10);
$colours = ['#ef4444', '#22c55e', '#eab308', '#06b6d4', '#a855f7', '#3b82f6', '#14b8a6', '#ec4899', '#f97316', '#ca8a04'];
$salesTotal = (float) ((ControllerProducts::ctrShowAddingOfTheSales()['total'] ?? 0));

$pieData = [];
foreach ($topProducts as $index => $product) {
    $pieData[] = [
        'value' => (int) $product['sales'],
        'color' => $colours[$index],
        'highlight' => $colours[$index],
        'label' => (string) $product['description'],
    ];
}
?>

<div class="box box-default">
  <div class="box-header with-border">
    <h3 class="box-title">Bestseller products</h3>
  </div>
  <div class="box-body">
    <?php if ($topProducts === []): ?>
      <div class="pos-inline-empty">Product performance will appear after inventory is added.</div>
    <?php else: ?>
      <div class="row">
        <div class="col-md-7"><div class="chart-responsive"><canvas id="pieChart" height="150"></canvas></div></div>
        <div class="col-md-5">
          <ul class="chart-legend clearfix">
            <?php foreach ($topProducts as $index => $product): ?>
              <li><i class="fa fa-circle" style="color:<?php echo e($colours[$index]); ?>"></i> <?php echo e($product['description']); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <?php if ($topProducts !== []): ?>
    <div class="box-footer no-padding">
      <ul class="nav nav-pills nav-stacked">
        <?php foreach (array_slice($topProducts, 0, 5) as $index => $product): ?>
          <?php $share = $salesTotal > 0 ? (int) ceil(((int) $product['sales'] * 100) / $salesTotal) : 0; ?>
          <li><a href="products">
            <img src="<?php echo e($product['image']); ?>" class="img-thumbnail" width="60" alt="">
            <?php echo e($product['description']); ?>
            <span class="pull-right" style="color:<?php echo e($colours[$index]); ?>"><?php echo $share; ?>%</span>
          </a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</div>

<?php if ($pieData !== []): ?>
<script>
(function () {
  var canvas = document.getElementById('pieChart');
  if (!canvas) return;
  var chart = new Chart(canvas.getContext('2d'));
  chart.Doughnut(<?php echo json_encode($pieData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>, {
    segmentShowStroke: true,
    segmentStrokeColor: '#fff',
    segmentStrokeWidth: 1,
    percentageInnerCutout: 50,
    animationSteps: 60,
    animationEasing: 'easeOutQuart',
    animateRotate: true,
    animateScale: false,
    responsive: true,
    maintainAspectRatio: false,
    tooltipTemplate: '<%=value %> <%=label%>'
  });
}());
</script>
<?php endif; ?>
