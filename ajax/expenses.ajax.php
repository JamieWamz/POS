<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../controllers/expenses.controller.php';
require_once __DIR__ . '/../models/expenses.model.php';

require_method('POST');
require_auth(['Administrator', 'Seller']);
require_csrf();
header('Content-Type: application/json; charset=utf-8');

$id = filter_input(INPUT_POST, 'idExpense', FILTER_VALIDATE_INT);
if (!$id) {
    abort_request(422, 'A valid expense is required.');
}
echo json_encode(ControllerExpenses::ctrShowExpenses('id', $id), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
