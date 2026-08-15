<?php

require_once __DIR__ . '/../../core/bootstrap.php';
require_auth(['Administrator', 'Seller']);

require_once __DIR__ . '/../../controllers/sales.controller.php';
require_once __DIR__ . '/../../models/sales.model.php';
require_once __DIR__ . '/../../controllers/customers.controller.php';
require_once __DIR__ . '/../../models/customers.model.php';
require_once __DIR__ . '/../../controllers/users.controller.php';
require_once __DIR__ . '/../../models/users.model.php';

$initialDate = valid_date(isset($_GET['initialDate']) ? (string) $_GET['initialDate'] : null);
$finalDate = valid_date(isset($_GET['finalDate']) ? (string) $_GET['finalDate'] : null);
if ((isset($_GET['initialDate']) || isset($_GET['finalDate'])) && (!$initialDate || !$finalDate || $initialDate > $finalDate)) {
    abort_request(422, 'Invalid report date range.');
}

(new ControllerSales())->ctrDownloadReport($initialDate, $finalDate);
