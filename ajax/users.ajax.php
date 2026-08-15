<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../controllers/users.controller.php';
require_once __DIR__ . '/../models/users.model.php';

require_method('POST');
require_auth(['Administrator']);
require_csrf();
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['activateUser'], $_POST['activateId'])) {
    $status = filter_var($_POST['activateUser'], FILTER_VALIDATE_INT);
    $id = filter_var($_POST['activateId'], FILTER_VALIDATE_INT);
    if (!in_array($status, [0, 1], true) || !$id || $id === (int) $_SESSION['id']) {
        abort_request(422, 'Invalid user status update.');
    }
    $result = UsersModel::mdlUpdateUser('users', 'status', $status, 'id', $id);
    if ($result === 'ok') {
        audit_event('user.status_changed', 'user', $id, ['status' => $status]);
    }
    echo json_encode(['ok' => $result === 'ok']);
    exit;
}

$field = isset($_POST['idUser']) ? 'id' : (isset($_POST['validateUser']) ? 'user' : null);
$value = $_POST['idUser'] ?? ($_POST['validateUser'] ?? null);
if ($field === null) {
    abort_request(422, 'No supported action was supplied.');
}

$user = ControllerUsers::ctrShowUsers($field, $value);
if (is_array($user)) {
    unset($user['password']);
}
echo json_encode($user, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
