<?php

require_once __DIR__ . '/../../core/bootstrap.php';
require_auth(['Administrator', 'Seller']);

require_once __DIR__ . '/../../controllers/sales.controller.php';
require_once __DIR__ . '/../../controllers/customers.controller.php';
require_once __DIR__ . '/../../controllers/users.controller.php';
require_once __DIR__ . '/../../models/sales.model.php';
require_once __DIR__ . '/../../models/customers.model.php';
require_once __DIR__ . '/../../models/users.model.php';

$code = filter_input(INPUT_GET, 'code', FILTER_VALIDATE_INT);
if (!$code) {
    abort_request(422, 'A valid sale code is required.');
}

$sale = ControllerSales::ctrShowSales('code', $code);
if (!$sale) {
    abort_request(404, 'Receipt not found.');
}

$customer = ControllerCustomers::ctrShowCustomers('id', $sale['idCustomer']);
$seller = ControllerUsers::ctrShowUsers('id', $sale['idSeller']);
$products = json_decode((string) $sale['products'], true);
$products = is_array($products) ? $products : [];
$business = app_config('business', []);
$currency = (string) ($business['currency'] ?? 'K');
$taxLabel = (string) ($business['tax_label'] ?? 'VAT');
$taxRate = isset($sale['taxRate'])
    ? (float) $sale['taxRate']
    : ((float) $sale['netPrice'] > 0 ? ((float) $sale['tax'] / (float) $sale['netPrice']) * 100 : 0);
$paymentParts = explode('-', (string) $sale['paymentMethod'], 2);
$paymentLabels = ['cash' => 'Cash', 'CC' => 'Mobile Money', 'DC' => 'Debit Card'];
$paymentLabel = $paymentLabels[$paymentParts[0]] ?? $paymentParts[0];
$autoPrint = filter_input(INPUT_GET, 'autoprint', FILTER_VALIDATE_BOOL);
audit_event('invoice.viewed', 'sale', $code, ['format' => 'print']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt #<?php echo (int) $sale['code']; ?></title>
  <style>
    :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
    * { box-sizing: border-box; }
    body { margin: 0; background: #eef2f7; color: #111827; }
    .toolbar { display: flex; justify-content: center; gap: 10px; padding: 18px; }
    .toolbar a, .toolbar button { border: 0; border-radius: 9px; padding: 10px 16px; cursor: pointer; text-decoration: none; font: inherit; font-weight: 700; }
    .toolbar a { background: #fff; color: #374151; }
    .toolbar button { background: #dc2626; color: #fff; }
    .receipt { width: min(100% - 24px, 360px); margin: 0 auto 32px; padding: 26px 22px; background: #fff; box-shadow: 0 18px 45px rgba(15,23,42,.14); }
    .brand { text-align: center; border-bottom: 2px dashed #d1d5db; padding-bottom: 18px; }
    .brand strong { display: block; font-size: 21px; letter-spacing: .08em; }
    .brand span, .muted { color: #6b7280; font-size: 12px; }
    .receipt-id { display: inline-block; margin-top: 12px; border-radius: 999px; padding: 6px 10px; background: #fee2e2; color: #991b1b; font-weight: 800; font-size: 12px; }
    .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 18px 0; }
    .meta span { display: block; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; }
    .meta strong { display: block; margin-top: 3px; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th { color: #6b7280; font-size: 10px; text-align: left; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding: 8px 3px; }
    td { border-bottom: 1px solid #f3f4f6; padding: 10px 3px; vertical-align: top; }
    th:nth-child(n+2), td:nth-child(n+2) { text-align: right; }
    .totals { margin-top: 14px; }
    .totals div { display: flex; justify-content: space-between; padding: 5px 3px; font-size: 12px; }
    .totals .grand { border-top: 2px solid #111827; margin-top: 6px; padding-top: 10px; font-size: 17px; font-weight: 800; }
    .thanks { margin-top: 20px; text-align: center; color: #6b7280; font-size: 11px; }
    @media print { body { background: #fff; } .toolbar { display: none; } .receipt { width: 80mm; margin: 0; padding: 4mm; box-shadow: none; } @page { size: 80mm auto; margin: 0; } }
  </style>
</head>
<body>
  <div class="toolbar"><a href="../../sales">Back to sales</a><button type="button" onclick="window.print()">Print receipt</button></div>
  <main class="receipt">
    <header class="brand">
      <strong><?php echo e($business['name'] ?? app_config('name', 'Golden Tap POS')); ?></strong>
      <?php if (!empty($business['address'])): ?><span><?php echo e($business['address']); ?></span><?php endif; ?>
      <?php if (!empty($business['phone'])): ?><span><?php echo e($business['phone']); ?></span><?php endif; ?>
      <?php if (!empty($business['tax_number'])): ?><span>TPIN: <?php echo e($business['tax_number']); ?></span><?php endif; ?>
      <div><span class="receipt-id">Receipt #<?php echo (int) $sale['code']; ?></span></div>
    </header>
    <section class="meta">
      <div><span>Date</span><strong><?php echo e(date('j M Y, H:i', strtotime((string) $sale['saledate']))); ?></strong></div>
      <div><span>Payment</span><strong><?php echo e($paymentLabel); ?></strong></div>
      <div><span>Customer</span><strong><?php echo e($customer['name'] ?? 'Walk-in Customer'); ?></strong></div>
      <div><span>Served by</span><strong><?php echo e($seller['name'] ?? 'System'); ?></strong></div>
    </section>
    <table>
      <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach ($products as $product): ?>
          <tr>
            <td><?php echo e($product['description'] ?? 'Product'); ?></td>
            <td><?php echo (int) ($product['quantity'] ?? 0); ?></td>
            <td><?php echo e($currency); ?><?php echo number_format((float) ($product['price'] ?? 0), 2); ?></td>
            <td><?php echo e($currency); ?><?php echo number_format((float) ($product['totalPrice'] ?? 0), 2); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <section class="totals">
      <div><span>Subtotal</span><strong><?php echo e($currency); ?><?php echo number_format((float) $sale['netPrice'], 2); ?></strong></div>
      <div><span><?php echo e($taxLabel); ?> (<?php echo number_format($taxRate, 2); ?>%)</span><strong><?php echo e($currency); ?><?php echo number_format((float) $sale['tax'], 2); ?></strong></div>
      <div class="grand"><span>Total</span><span><?php echo e($currency); ?><?php echo number_format((float) $sale['totalPrice'], 2); ?></span></div>
      <?php if ($paymentParts[0] === 'cash'): ?>
        <div><span>Cash tendered</span><strong><?php echo e($currency); ?><?php echo number_format((float) ($sale['amountTendered'] ?? $sale['totalPrice']), 2); ?></strong></div>
        <div><span>Change due</span><strong><?php echo e($currency); ?><?php echo number_format((float) ($sale['changeDue'] ?? 0), 2); ?></strong></div>
      <?php elseif (isset($paymentParts[1])): ?>
        <div><span>Reference</span><strong><?php echo e($paymentParts[1]); ?></strong></div>
      <?php endif; ?>
    </section>
    <footer class="thanks">Thank you for your business.</footer>
  </main>
  <?php if ($autoPrint): ?><script>window.addEventListener('load', function () { window.print(); });</script><?php endif; ?>
</body>
</html>
