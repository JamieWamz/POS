<?php

require_once __DIR__ . '/../core/bootstrap.php';

class ControllerProducts
{
    public static function ctrShowProducts($item, $value, $order)
    {
        return ProductsModel::mdlShowProducts('products', $item, $value, $order);
    }

    private static function productData(string $prefix): ?array
    {
        $category = filter_var($_POST[$prefix . 'Category'] ?? null, FILTER_VALIDATE_INT);
        $code = trim((string) ($_POST[$prefix . 'Code'] ?? ''));
        $description = trim((string) ($_POST[$prefix . 'Description'] ?? ''));
        $stock = filter_var($_POST[$prefix . 'Stock'] ?? null, FILTER_VALIDATE_INT);
        $buyingPrice = filter_var($_POST[$prefix . 'BuyingPrice'] ?? null, FILTER_VALIDATE_FLOAT);
        $sellingPrice = filter_var($_POST[$prefix . 'SellingPrice'] ?? null, FILTER_VALIDATE_FLOAT);
        if (!$category
            || !preg_match('/^[A-Za-z0-9_-]{1,40}$/', $code)
            || !preg_match('/^[\p{L}\p{N} &,.\'()\/-]{2,160}$/u', $description)
            || $stock === false || $stock < 0
            || $buyingPrice === false || $buyingPrice < 0
            || $sellingPrice === false || $sellingPrice <= 0) {
            return null;
        }
        return [
            'idCategory' => $category,
            'code' => $code,
            'description' => $description,
            'stock' => $stock,
            'buyingPrice' => number_format((float) $buyingPrice, 2, '.', ''),
            'sellingPrice' => number_format((float) $sellingPrice, 2, '.', ''),
        ];
    }

    public static function ctrCreateProducts(): void
    {
        if (!isset($_POST['newDescription'])) {
            return;
        }
        require_auth(['Administrator', 'Special']);
        $data = self::productData('new');
        if (!$data || ProductsModel::mdlShowProducts('products', 'code', $data['code'], 'id')) {
            ui_alert('error', 'Check the product details; the code must be unique.', 'products');
            return;
        }
        try {
            $data['image'] = store_uploaded_image($_FILES['newProdPhoto'] ?? [], 'views/img/products', $data['code'])
                ?? 'views/img/products/default/anonymous.png';
            $result = ProductsModel::mdlAddProduct('products', $data);
            if ($result === 'ok') { audit_event('product.created', 'product', $data['code']); }
            ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Product saved.' : 'Product could not be saved.', 'products');
        } catch (Throwable $error) {
            ui_alert('error', app_config('debug') ? $error->getMessage() : 'Product could not be saved.', 'products');
        }
    }

    public static function ctrEditProduct(): void
    {
        if (!isset($_POST['editDescription'])) {
            return;
        }
        require_auth(['Administrator', 'Special']);
        $data = self::productData('edit');
        $existing = $data ? ProductsModel::mdlShowProducts('products', 'code', $data['code'], 'id') : false;
        if (!$data || !$existing) {
            ui_alert('error', 'Check the product details and try again.', 'products');
            return;
        }
        try {
            $data['image'] = (string) $existing['image'];
            $uploaded = store_uploaded_image($_FILES['editImage'] ?? [], 'views/img/products', $data['code']);
            if ($uploaded !== null) {
                if ($data['image'] !== 'views/img/products/default/anonymous.png') {
                    safe_managed_file_delete($data['image'], 'views/img/products');
                }
                $data['image'] = $uploaded;
            }
            $result = ProductsModel::mdlEditProduct('products', $data);
            if ($result === 'ok') { audit_event('product.updated', 'product', $data['code']); }
            ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Product updated.' : 'Product could not be updated.', 'products');
        } catch (Throwable $error) {
            ui_alert('error', app_config('debug') ? $error->getMessage() : 'Product could not be updated.', 'products');
        }
    }

    public static function ctrDeleteProduct(): void
    {
        if (!isset($_POST['deleteProductId'])) {
            return;
        }
        require_auth(['Administrator']);
        $id = filter_var($_POST['deleteProductId'], FILTER_VALIDATE_INT);
        $existing = $id ? ProductsModel::mdlShowProducts('products', 'id', $id, 'id') : false;
        if (!$existing) {
            ui_alert('error', 'Product not found.', 'products');
            return;
        }
        if ($existing['image'] !== 'views/img/products/default/anonymous.png') {
            safe_managed_file_delete((string) $existing['image'], 'views/img/products');
        }
        $result = ProductsModel::mdlDeleteProduct('products', $id);
        if ($result === 'ok') { audit_event('product.deleted', 'product', $id); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Product deleted.' : 'Product could not be deleted.', 'products');
    }

    public static function ctrShowAddingOfTheSales()
    {
        return ProductsModel::mdlShowAddingOfTheSales('products');
    }
}
