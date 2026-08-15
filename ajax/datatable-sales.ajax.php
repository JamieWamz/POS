<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../controllers/products.controller.php';
require_once __DIR__ . '/../models/products.model.php';

require_auth(['Administrator', 'Seller']);
header('Content-Type: application/json; charset=utf-8');

$rows = [];
$products = ControllerProducts::ctrShowProducts(null, null, 'id');
foreach ($products ?: [] as $index => $product) {
    $stock = (int) $product['stock'];
    $stockClass = $stock <= 10 ? 'danger' : ($stock <= 15 ? 'warning' : 'success');
    $rows[] = [
        $index + 1,
        '<img src="' . e($product['image']) . '" alt="" width="44" height="44" class="pos-table-thumb">',
        e($product['code']),
        e($product['description']),
        '<span class="label label-' . $stockClass . '">' . $stock . '</span>',
        '<button type="button" class="btn btn-primary addProductSale recoverButton" idProduct="' . (int) $product['id'] . '" aria-label="Add product"><i class="fa fa-plus"></i></button>',
    ];
}

echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
