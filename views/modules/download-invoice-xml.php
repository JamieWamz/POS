<?php

require_once __DIR__ . '/../../core/bootstrap.php';
require_auth(['Administrator', 'Seller']);

require_once __DIR__ . '/../../controllers/sales.controller.php';
require_once __DIR__ . '/../../models/sales.model.php';
require_once __DIR__ . '/../../models/products.model.php';
require_once __DIR__ . '/../../models/customers.model.php';
require_once __DIR__ . '/../../models/users.model.php';

$code = filter_input(INPUT_GET, 'code', FILTER_VALIDATE_INT);
if (!$code) {
    abort_request(422, 'A valid sale code is required.');
}
$content = ControllerSales::ctrDownloadXML((int) $code);
if ($content === false) {
    abort_request(404, 'Invoice not found.');
}
header('Content-Type: application/xml; charset=utf-8');
header('Content-Disposition: attachment; filename="invoice-' . $code . '.xml"');
header('Content-Length: ' . strlen($content));
echo $content;
exit;
