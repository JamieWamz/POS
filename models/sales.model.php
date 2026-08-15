<?php

require_once __DIR__ . '/connection.php';

class ModelSales
{
    public static function mdlShowSales($table, $item, $value)
    {
        if ($item !== null && !in_array($item, ['id', 'code'], true)) {
            throw new InvalidArgumentException('Unsupported sale lookup field.');
        }
        if ($item !== null) {
            $stmt = Connection::connect()->prepare("SELECT * FROM sales WHERE {$item} = :value LIMIT 1");
            $stmt->execute(['value' => $value]);
            return $stmt->fetch() ?: false;
        }
        return Connection::connect()->query('SELECT * FROM sales ORDER BY id ASC')->fetchAll();
    }

    public static function mdlLockSaleById(int $id)
    {
        $stmt = Connection::connect()->prepare('SELECT * FROM sales WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: false;
    }

    public static function mdlLockSaleByCode(int $code)
    {
        $stmt = Connection::connect()->prepare('SELECT * FROM sales WHERE code = :code FOR UPDATE');
        $stmt->execute(['code' => $code]);
        return $stmt->fetch() ?: false;
    }

    public static function mdlNextCode(): int
    {
        $value = Connection::connect()->query('SELECT code FROM sales ORDER BY code DESC LIMIT 1 FOR UPDATE')->fetchColumn();
        return $value === false ? 10001 : ((int) $value + 1);
    }

    public static function mdlAddSale($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'INSERT INTO sales(code, idCustomer, idSeller, products, taxRate, tax, netPrice, totalPrice, paymentMethod, amountTendered, changeDue) VALUES (:code, :idCustomer, :idSeller, :products, :taxRate, :tax, :netPrice, :totalPrice, :paymentMethod, :amountTendered, :changeDue)'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlEditSale($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'UPDATE sales SET idCustomer = :idCustomer, idSeller = :idSeller, products = :products, taxRate = :taxRate, tax = :tax, netPrice = :netPrice, totalPrice = :totalPrice, paymentMethod = :paymentMethod, amountTendered = :amountTendered, changeDue = :changeDue WHERE code = :code'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlDeleteSale($table, $data)
    {
        $stmt = Connection::connect()->prepare('DELETE FROM sales WHERE id = :id');
        return $stmt->execute(['id' => (int) $data]) ? 'ok' : 'error';
    }

    public static function mdlSalesDatesRange($table, $initialDate, $finalDate)
    {
        if ($initialDate === null || $finalDate === null) {
            return Connection::connect()->query('SELECT * FROM sales ORDER BY id ASC')->fetchAll();
        }
        $endExclusive = (new DateTimeImmutable($finalDate))->modify('+1 day')->format('Y-m-d');
        $stmt = Connection::connect()->prepare(
            'SELECT * FROM sales WHERE saledate >= :initialDate AND saledate < :endDate ORDER BY id ASC'
        );
        $stmt->execute(['initialDate' => $initialDate, 'endDate' => $endExclusive]);
        return $stmt->fetchAll();
    }

    public static function mdlAddingTotalSales($table)
    {
        return Connection::connect()->query('SELECT COALESCE(SUM(netPrice), 0) AS total FROM sales')->fetch();
    }
}
