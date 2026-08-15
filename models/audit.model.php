<?php

require_once __DIR__ . '/connection.php';

class ModelAudit
{
    public static function log(string $action, string $entityType, int|string|null $entityId = null, array $metadata = []): void
    {
        try {
            $stmt = Connection::connect()->prepare(
                'INSERT INTO activity_logs(user_id, action, entity_type, entity_id, metadata, ip_address) VALUES (:user_id, :action, :entity_type, :entity_id, :metadata, :ip_address)'
            );
            $stmt->execute([
                'user_id' => isset($_SESSION['id']) ? (int) $_SESSION['id'] : null,
                'action' => substr($action, 0, 80),
                'entity_type' => substr($entityType, 0, 50),
                'entity_id' => $entityId === null ? null : substr((string) $entityId, 0, 80),
                'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'ip_address' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]);
        } catch (Throwable $error) {
            if (app_config('debug')) {
                error_log('Audit log write failed: ' . $error->getMessage());
            }
        }
    }

    public static function recent(int $limit = 10): array
    {
        try {
            $limit = max(1, min($limit, 200));
            $stmt = Connection::connect()->prepare(
                "SELECT activity_logs.*, users.name AS user_name FROM activity_logs LEFT JOIN users ON users.id = activity_logs.user_id ORDER BY activity_logs.id DESC LIMIT {$limit}"
            );
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Throwable $error) {
            return [];
        }
    }
}
