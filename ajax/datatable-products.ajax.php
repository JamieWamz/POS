<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../controllers/products.controller.php';
require_once __DIR__ . '/../models/products.model.php';
require_once __DIR__ . '/../controllers/categories.controller.php';
require_once __DIR__ . '/../models/categories.model.php';

require_auth(['Administrator', 'Special']);
header('Content-Type: application/json; charset=utf-8');

$rows = [];
$products = ControllerProducts::ctrShowProducts(null, null, 'id');
foreach ($products ?: [] as $index => $product) {
    $category = ControllerCategories::ctrShowCategories('id', $product['idCategory']);
    $stock = (int) $product['stock'];
    $stockClass = $stock <= 10 ? 'danger' : ($stock <= 15 ? 'warning' : 'success');
    $image = '<img src="' . e($product['image']) . '" alt="" width="44" height="44" class="pos-table-thumb">';
    $buttons = '<div class="btn-group"><button type="button" class="btn btn-primary btnEditProduct" idProduct="' . (int) $product['id'] . '" data-toggle="modal" data-target="#modalEditProduct" aria-label="Edit product"><i class="fa fa-pencil"></i></button>';
    if (current_user_role() === 'Administrator') {
        $buttons .= '<button type="button" class="btn btn-danger btnDeleteProduct" idProduct="' . (int) $product['id'] . '" aria-label="Delete product"><i class="fa fa-trash"></i></button>';
    }
    $buttons .= '</div>';

    $rows[] = [
        $index + 1,
        $image,
        e($product['code']),
        e($product['description']),
        e($category['Category'] ?? ''),
        '<span class="label label-' . $stockClass . '">' . $stock . '</span>',
        'K ' . number_format((float) $product['buyingPrice'], 2),
        'K ' . number_format((float) $product['sellingPrice'], 2),
        e($product['date']),
        $buttons,
    ];
}

echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
