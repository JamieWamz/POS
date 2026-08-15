<?php

require_once __DIR__ . '/core/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_csrf();
}

require_once __DIR__ . '/controllers/template.controller.php';
require_once __DIR__ . '/controllers/users.controller.php';
require_once __DIR__ . '/controllers/categories.controller.php';
require_once __DIR__ . '/controllers/products.controller.php';
require_once __DIR__ . '/controllers/customers.controller.php';
require_once __DIR__ . '/controllers/sales.controller.php';

require_once __DIR__ . '/models/users.model.php';
require_once __DIR__ . '/models/categories.model.php';
require_once __DIR__ . '/models/products.model.php';
require_once __DIR__ . '/models/customers.model.php';
require_once __DIR__ . '/models/sales.model.php';

$template = new ControllerTemplate();
$template->ctrTemplate();
