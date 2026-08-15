<?php

require_once __DIR__ . '/connection.php';

class ModelCustomers
{
    public static function mdlAddCustomer($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'INSERT INTO customers(name, email, phone, address, birthdate) VALUES (:name, :email, :phone, :address, :birthdate)'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlEditCustomer($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'UPDATE customers SET name = :name, email = :email, phone = :phone, address = :address, birthdate = :birthdate WHERE id = :id'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlShowCustomers($table, $item, $value)
    {
        if ($item !== null && $item !== 'id') {
            throw new InvalidArgumentException('Unsupported customer lookup field.');
        }
        if ($item === 'id') {
            $stmt = Connection::connect()->prepare('SELECT * FROM customers WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => (int) $value]);
            return $stmt->fetch() ?: false;
        }
        return Connection::connect()->query('SELECT * FROM customers ORDER BY name ASC')->fetchAll();
    }

    public static function mdlDeleteCustomer($table, $data)
    {
        $used = Connection::connect()->prepare('SELECT COUNT(*) FROM sales WHERE idCustomer = :id');
        $used->execute(['id' => (int) $data]);
        if ((int) $used->fetchColumn() > 0) {
            return 'in_use';
        }
        $stmt = Connection::connect()->prepare('DELETE FROM customers WHERE id = :id');
        return $stmt->execute(['id' => (int) $data]) ? 'ok' : 'error';
    }

    public static function mdlUpdateCustomer($table, $item1, $value1, $value)
    {
        if (!in_array($item1, ['purchases', 'lastPurchase'], true)) {
            throw new InvalidArgumentException('Unsupported customer update field.');
        }
        $stmt = Connection::connect()->prepare("UPDATE customers SET {$item1} = :newValue WHERE id = :id");
        return $stmt->execute(['newValue' => $value1, 'id' => (int) $value]) ? 'ok' : 'error';
    }
}
