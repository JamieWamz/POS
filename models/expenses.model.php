<?php

require_once __DIR__ . '/connection.php';

class ModelExpenses
{
    public static function mdlAddExpense($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'INSERT INTO expenses(description, category, amount, id_user) VALUES (:description, :category, :amount, :id_user)'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlShowExpenses($table, $item, $value)
    {
        if ($item !== null && $item !== 'id') {
            throw new InvalidArgumentException('Unsupported expense lookup field.');
        }
        if ($item === 'id') {
            $stmt = Connection::connect()->prepare('SELECT * FROM expenses WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => (int) $value]);
            return $stmt->fetch() ?: false;
        }
        return Connection::connect()->query('SELECT * FROM expenses ORDER BY id DESC')->fetchAll();
    }

    public static function mdlEditExpense($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'UPDATE expenses SET description = :description, category = :category, amount = :amount WHERE id = :id'
        );
        return $stmt->execute($data) ? 'ok' : 'error';
    }

    public static function mdlDeleteExpense($table, $data)
    {
        $stmt = Connection::connect()->prepare('DELETE FROM expenses WHERE id = :id');
        return $stmt->execute(['id' => (int) $data]) ? 'ok' : 'error';
    }

    public static function mdlExpensesDatesRange($table, $initialDate, $finalDate)
    {
        if ($initialDate === null || $finalDate === null) {
            return Connection::connect()->query('SELECT * FROM expenses ORDER BY id ASC')->fetchAll();
        }
        $endExclusive = (new DateTimeImmutable($finalDate))->modify('+1 day')->format('Y-m-d');
        $stmt = Connection::connect()->prepare(
            'SELECT * FROM expenses WHERE date >= :initialDate AND date < :endDate ORDER BY id ASC'
        );
        $stmt->execute(['initialDate' => $initialDate, 'endDate' => $endExclusive]);
        return $stmt->fetchAll();
    }
}
