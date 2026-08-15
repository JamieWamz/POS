<?php

require_once __DIR__ . '/connection.php';

class UsersModel
{
    private static function assertField(?string $field): void
    {
        if ($field !== null && !in_array($field, ['id', 'user', 'status'], true)) {
            throw new InvalidArgumentException('Unsupported user lookup field.');
        }
    }

    public static function MdlShowUsers($tableUsers, $item, $value)
    {
        self::assertField($item);
        $db = Connection::connect();

        if ($item !== null) {
            $stmt = $db->prepare("SELECT * FROM users WHERE {$item} = :value LIMIT 1");
            $stmt->execute(['value' => $value]);
            return $stmt->fetch() ?: false;
        }

        return $db->query('SELECT * FROM users ORDER BY name ASC')->fetchAll();
    }

    public static function mdlAddUser($table, $data)
    {
        $stmt = Connection::connect()->prepare(
            'INSERT INTO users(name, user, password, profile, photo, status) VALUES (:name, :user, :password, :profile, :photo, 1)'
        );
        return $stmt->execute([
            'name' => $data['name'],
            'user' => $data['user'],
            'password' => $data['password'],
            'profile' => $data['profile'],
            'photo' => $data['photo'],
        ]) ? 'ok' : 'error';
    }

    public static function mdlEditUser($table, $data)
    {
        $fields = 'name = :name, profile = :profile, photo = :photo';
        $params = [
            'name' => $data['name'],
            'profile' => $data['profile'],
            'photo' => $data['photo'],
            'user' => $data['user'],
        ];
        if (!empty($data['password'])) {
            $fields .= ', password = :password';
            $params['password'] = $data['password'];
        }

        $stmt = Connection::connect()->prepare("UPDATE users SET {$fields} WHERE user = :user");
        return $stmt->execute($params) ? 'ok' : 'error';
    }

    public static function mdlUpdateUser($table, $item1, $value1, $item2, $value2)
    {
        $allowedUpdates = ['lastLogin', 'status', 'password'];
        $allowedWhere = ['id', 'user'];
        if (!in_array($item1, $allowedUpdates, true) || !in_array($item2, $allowedWhere, true)) {
            throw new InvalidArgumentException('Unsupported user update.');
        }

        $stmt = Connection::connect()->prepare("UPDATE users SET {$item1} = :newValue WHERE {$item2} = :matchValue");
        return $stmt->execute(['newValue' => $value1, 'matchValue' => $value2]) ? 'ok' : 'error';
    }

    public static function mdlDeleteUser($table, $data)
    {
        $used = Connection::connect()->prepare(
            'SELECT (SELECT COUNT(*) FROM sales WHERE idSeller = :sales_id) + (SELECT COUNT(*) FROM expenses WHERE id_user = :expense_id)'
        );
        $used->execute(['sales_id' => (int) $data, 'expense_id' => (int) $data]);
        if ((int) $used->fetchColumn() > 0) {
            return 'in_use';
        }
        $stmt = Connection::connect()->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => (int) $data]) ? 'ok' : 'error';
    }
}
