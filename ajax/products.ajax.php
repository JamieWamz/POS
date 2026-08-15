<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../controllers/products.controller.php';
require_once __DIR__ . '/../models/products.model.php';

require_method('POST');
require_auth(['Administrator', 'Special', 'Seller']);
require_csrf();
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['idCategory'])) {
    $answer = ControllerProducts::ctrShowProducts('idCategory', (int) $_POST['idCategory'], 'id');
} elseif (isset($_POST['idProduct'])) {
    $answer = ControllerProducts::ctrShowProducts('id', (int) $_POST['idProduct'], 'id');
} elseif (isset($_POST['getProducts']) && $_POST['getProducts'] === 'ok') {
    $answer = ControllerProducts::ctrShowProducts(null, null, 'id');
} elseif (isset($_POST['productName'])) {
    $answer = ControllerProducts::ctrShowProducts('description', (string) $_POST['productName'], 'id');
} else {
    abort_request(422, 'No supported product action was supplied.');
}

echo json_encode($answer, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
