<?php

require_once __DIR__ . '/connection.php';

class CategoriesModel
{
    public static function mdlAddCategory($table, $data)
    {
        $stmt = Connection::connect()->prepare('INSERT INTO categories(Category) VALUES (:category)');
        return $stmt->execute(['category' => $data]) ? 'ok' : 'error';
    }

    public static function mdlShowCategories($table, $item, $value)
    {
        if ($item !== null && $item !== 'id') {
            throw new InvalidArgumentException('Unsupported category lookup field.');
        }
        if ($item === 'id') {
            $stmt = Connection::connect()->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => (int) $value]);
            return $stmt->fetch() ?: false;
        }
        return Connection::connect()->query('SELECT * FROM categories ORDER BY Category ASC')->fetchAll();
    }

    public static function mdlEditCategory($table, $data)
    {
        $stmt = Connection::connect()->prepare('UPDATE categories SET Category = :category WHERE id = :id');
        return $stmt->execute(['category' => $data['Category'], 'id' => (int) $data['id']]) ? 'ok' : 'error';
    }

    public static function mdlDeleteCategory($table, $data)
    {
        $inUse = Connection::connect()->prepare('SELECT COUNT(*) FROM products WHERE idCategory = :id');
        $inUse->execute(['id' => (int) $data]);
        if ((int) $inUse->fetchColumn() > 0) {
            return 'in_use';
        }
        $stmt = Connection::connect()->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute(['id' => (int) $data]) ? 'ok' : 'error';
    }
}
