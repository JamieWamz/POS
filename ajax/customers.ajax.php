<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../controllers/customers.controller.php';
require_once __DIR__ . '/../models/customers.model.php';

require_method('POST');
require_auth(['Administrator', 'Seller']);
require_csrf();
header('Content-Type: application/json; charset=utf-8');

$id = filter_input(INPUT_POST, 'idCustomer', FILTER_VALIDATE_INT);
if (!$id) {
    abort_request(422, 'A valid customer is required.');
}
echo json_encode(ControllerCustomers::ctrShowCustomers('id', $id), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
