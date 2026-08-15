<?php

require_once __DIR__ . '/connection.php';

class ProductsModel
{
    public static function mdlShowProducts($table, $item, $value, $order)
    {
        $allowedFields = ['id', 'idCategory', 'code', 'description'];
        $allowedOrders = ['id', 'stock', 'sales', 'date', 'description'];
        if ($item !== null && !in_array($item, $allowedFields, true)) {
            throw new InvalidArgumentException('Unsupported product lookup field.');
        }
        if (!in_array($order, $allowedOrders, true)) {
            $order = 'id';
        }

        if ($item !== null) {
            $limit = $item === 'idCategory' ? '' : ' LIMIT 1';
            $stmt = Connection::connect()->prepare("SELECT * FROM products WHERE {$item} = :value ORDER BY {$order} DESC{$limit}");
            $stmt->execute(['value' => $value]);
            if ($item === 'idCategory') {
                return $stmt->fetchAll();
            }
            return $stmt->fetch() ?: false;
        }
        return Connection::connect()->query("SELECT * FROM products ORDER BY {$order} DESC")->fetchAll();
    }

    public static function mdlLockProduct(int $id)
    {
        $stmt = Connection::connect()->prepare('SELECT * FROM products WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: false;
    }

    public static function mdlAddProduct($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'INSERT INTO products(idCategory, code, description, image, stock, buyingPrice, sellingPrice, sales) VALUES (:idCategory, :code, :description, :image, :stock, :buyingPrice, :sellingPrice, 0)'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlEditProduct($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'UPDATE products SET idCategory = :idCategory, description = :description, image = :image, stock = :stock, buyingPrice = :buyingPrice, sellingPrice = :sellingPrice WHERE code = :code'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlDeleteProduct($table, $data)
    {
        $stmt = Connection::connect()->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => (int) $data]) ? 'ok' : 'error';
    }

    public static function mdlUpdateProduct($table, $item1, $value1, $value)
    {
        if (!in_array($item1, ['stock', 'sales'], true)) {
            throw new InvalidArgumentException('Unsupported inventory update field.');
        }
        $stmt = Connection::connect()->prepare("UPDATE products SET {$item1} = :newValue WHERE id = :id");
        return $stmt->execute(['newValue' => $value1, 'id' => (int) $value]) ? 'ok' : 'error';
    }

    public static function mdlSetInventory(int $id, int $stock, int $sales): void
    {
        $stmt = Connection::connect()->prepare('UPDATE products SET stock = :stock, sales = :sales WHERE id = :id');
        $stmt->execute(['stock' => $stock, 'sales' => $sales, 'id' => $id]);
    }

    public static function mdlShowAddingOfTheSales($table)
    {
        return Connection::connect()->query('SELECT COALESCE(SUM(sales), 0) AS total FROM products')->fetch();
    }
}
