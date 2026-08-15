<?php
require_once __DIR__ . '/../../../models/audit.model.php';
$allProducts = ControllerProducts::ctrShowProducts(null, null, 'id') ?: [];
$stockAlerts = array_values(array_filter($allProducts, static fn ($product) => (int) $product['stock'] <= 10));
usort($stockAlerts, static fn ($left, $right) => (int) $left['stock'] <=> (int) $right['stock']);
$allSales = ControllerSales::ctrShowSales(null, null) ?: [];
$recentSales = array_slice(array_reverse($allSales), 0, 6);
$recentActivity = ModelAudit::recent(7);
?>
<div class="row pos-admin-grid">
  <div class="col-lg-8 col-xs-12">
    <section class="box">
      <header class="box-header">
        <div><span class="pos-eyebrow">Sales</span><h3 class="box-title">Recent transactions</h3></div>
        <a class="btn btn-default" href="sales">View all</a>
      </header>
      <div class="box-body table-responsive no-padding">
        <table class="table pos-compact-table">
          <thead><tr><th>Sale</th><th>Customer</th><th>Payment</th><th>Total</th><th>Time</th></tr></thead>
          <tbody>
            <?php if ($recentSales === []): ?><tr><td colspan="5" class="text-center text-muted">No transactions yet.</td></tr><?php endif; ?>
            <?php foreach ($recentSales as $sale): ?>
              <?php $customer = ControllerCustomers::ctrShowCustomers('id', $sale['idCustomer']); ?>
              <tr>
                <td><strong>#<?php echo e($sale['code']); ?></strong></td>
                <td><?php echo e($customer['name'] ?? 'Walk-in Customer'); ?></td>
                <td><span class="pos-pill"><?php echo e(explode('-', $sale['paymentMethod'])[0]); ?></span></td>
                <td><strong>K<?php echo number_format((float) $sale['totalPrice'], 2); ?></strong></td>
                <td><?php echo e(date('H:i', strtotime($sale['saledate']))); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
  <div class="col-lg-4 col-xs-12">
    <section class="box pos-quick-actions">
      <header class="box-header"><div><span class="pos-eyebrow">Shortcuts</span><h3 class="box-title">Common tasks</h3></div></header>
      <div class="box-body">
        <a href="create-sale"><i class="fa fa-plus-circle"></i><span><strong>Start a sale</strong><small>Open the checkout</small></span><i class="fa fa-chevron-right"></i></a>
        <a href="products"><i class="fa fa-cube"></i><span><strong>Update inventory</strong><small>Prices, stock, and products</small></span><i class="fa fa-chevron-right"></i></a>
        <a href="expenses"><i class="fa fa-credit-card"></i><span><strong>Record an expense</strong><small>Keep net performance current</small></span><i class="fa fa-chevron-right"></i></a>
        <a href="users"><i class="fa fa-user-circle"></i><span><strong>Manage the team</strong><small>Roles and account status</small></span><i class="fa fa-chevron-right"></i></a>
      </div>
    </section>
  </div>
</div>
<div class="row pos-admin-grid">
  <div class="col-lg-6 col-xs-12">
    <section class="box">
      <header class="box-header">
        <div><span class="pos-eyebrow">Inventory</span><h3 class="box-title">Low-stock items</h3></div>
        <span class="pos-count-badge"><?php echo count($stockAlerts); ?></span>
      </header>
      <div class="box-body pos-alert-list">
        <?php if ($stockAlerts === []): ?><div class="pos-inline-empty"><i class="fa fa-check-circle"></i> Inventory levels look healthy.</div><?php endif; ?>
        <?php foreach (array_slice($stockAlerts, 0, 7) as $product): ?>
          <a href="products">
            <img src="<?php echo e($product['image']); ?>" alt="">
            <span><strong><?php echo e($product['description']); ?></strong><small>Code <?php echo e($product['code']); ?></small></span>
            <b class="<?php echo (int) $product['stock'] === 0 ? 'text-red' : 'text-orange'; ?>"><?php echo (int) $product['stock']; ?> left</b>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
  <div class="col-lg-6 col-xs-12">
    <section class="box">
      <header class="box-header"><div><span class="pos-eyebrow">Administration</span><h3 class="box-title">Recent account activity</h3></div></header>
      <div class="box-body pos-activity-list">
        <?php if ($recentActivity === []): ?><div class="pos-inline-empty">Activity appears after the database migration is applied.</div><?php endif; ?>
        <?php foreach ($recentActivity as $activity): ?>
          <div><span class="pos-activity-icon"><i class="fa fa-history"></i></span><p><strong><?php echo e($activity['user_name'] ?? 'System'); ?></strong> <?php echo e(str_replace('.', ' ', $activity['action'])); ?><small><?php echo e(date('j M, H:i', strtotime($activity['created_at']))); ?></small></p></div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>
