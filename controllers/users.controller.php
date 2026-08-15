<?php

require_once __DIR__ . '/../core/bootstrap.php';

class ControllerUsers
{
    private const PROFILES = ['Administrator', 'Special', 'Seller'];
    private const LEGACY_SALT = '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$';

    public static function ctrUserLogin(): void
    {
        if (!isset($_POST['loginUser'])) {
            return;
        }

        $username = trim((string) $_POST['loginUser']);
        $password = (string) ($_POST['loginPass'] ?? '');
        $blockedUntil = (int) ($_SESSION['login_blocked_until'] ?? 0);
        if ($blockedUntil > time()) {
            echo '<div class="alert alert-danger">Too many sign-in attempts. Try again shortly.</div>';
            return;
        }
        if (!preg_match('/^[a-zA-Z0-9_.-]{2,50}$/', $username) || $password === '' || strlen($password) > 256) {
            echo '<div class="alert alert-danger">Invalid username or password.</div>';
            return;
        }

        $answer = UsersModel::MdlShowUsers('users', 'user', $username);
        $storedHash = is_array($answer) ? (string) ($answer['password'] ?? '') : '';
        $modernMatch = $storedHash !== '' && password_verify($password, $storedHash);
        $legacyHash = crypt($password, self::LEGACY_SALT);
        $legacyMatch = $storedHash !== '' && hash_equals($storedHash, $legacyHash);

        if (!$answer || (!$modernMatch && !$legacyMatch) || (int) $answer['status'] !== 1) {
            $attempts = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['login_attempts'] = $attempts;
            if ($attempts >= 5) {
                $_SESSION['login_blocked_until'] = time() + min(300, 30 * ($attempts - 4));
            }
            usleep(250000);
            echo '<div class="alert alert-danger">Invalid username or password.</div>';
            return;
        }

        session_regenerate_id(true);
        unset($_SESSION['login_attempts'], $_SESSION['login_blocked_until']);
        $_SESSION['loggedIn'] = 'ok';
        $_SESSION['id'] = (int) $answer['id'];
        $_SESSION['name'] = (string) $answer['name'];
        $_SESSION['user'] = (string) $answer['user'];
        $_SESSION['photo'] = (string) $answer['photo'];
        $_SESSION['profile'] = (string) $answer['profile'];

        if ($legacyMatch || password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
            UsersModel::mdlUpdateUser('users', 'password', password_hash($password, PASSWORD_DEFAULT), 'id', $answer['id']);
        }
        UsersModel::mdlUpdateUser('users', 'lastLogin', date('Y-m-d H:i:s'), 'id', $answer['id']);

        audit_event('auth.login', 'user', (int) $answer['id']);

        echo '<script>window.location = "home";</script>';
    }

    public static function ctrCreateUser(): void
    {
        if (!isset($_POST['newUser'])) {
            return;
        }
        require_auth(['Administrator']);

        $name = trim((string) ($_POST['newName'] ?? ''));
        $username = trim((string) $_POST['newUser']);
        $password = (string) ($_POST['newPasswd'] ?? '');
        $profile = (string) ($_POST['newProfile'] ?? '');
        if (!preg_match('/^[\p{L}\p{N} .\'-]{2,100}$/u', $name)
            || !preg_match('/^[a-zA-Z0-9_.-]{2,50}$/', $username)
            || strlen($password) < 12 || strlen($password) > 256
            || !in_array($profile, self::PROFILES, true)) {
            ui_alert('error', 'Use valid details and a password of at least 12 characters.', 'users');
            return;
        }
        if (UsersModel::MdlShowUsers('users', 'user', $username)) {
            ui_alert('error', 'That username is already in use.', 'users');
            return;
        }

        try {
            $photo = store_uploaded_image($_FILES['newPhoto'] ?? [], 'views/img/users', $username) ?? '';
            $result = UsersModel::mdlAddUser('users', [
                'name' => $name,
                'user' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'profile' => $profile,
                'photo' => $photo,
            ]);
            if ($result === 'ok') {
                audit_event('user.created', 'user', $username, ['profile' => $profile]);
            }
            ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'User added successfully.' : 'The user could not be added.', 'users');
        } catch (Throwable $error) {
            ui_alert('error', app_config('debug') ? $error->getMessage() : 'The user could not be added.', 'users');
        }
    }

    public static function ctrShowUsers($item, $value)
    {
        return UsersModel::MdlShowUsers('users', $item, $value);
    }

    public static function ctrEditUser(): void
    {
        if (!isset($_POST['EditUser'])) {
            return;
        }
        require_auth(['Administrator']);

        $username = trim((string) $_POST['EditUser']);
        $existing = UsersModel::MdlShowUsers('users', 'user', $username);
        $name = trim((string) ($_POST['EditName'] ?? ''));
        $profile = (string) ($_POST['EditProfile'] ?? '');
        $password = (string) ($_POST['EditPasswd'] ?? '');
        if (!$existing || !preg_match('/^[\p{L}\p{N} .\'-]{2,100}$/u', $name) || !in_array($profile, self::PROFILES, true)) {
            ui_alert('error', 'The submitted user details are invalid.', 'users');
            return;
        }
        if ($password !== '' && (strlen($password) < 12 || strlen($password) > 256)) {
            ui_alert('error', 'New passwords must be between 12 and 256 characters.', 'users');
            return;
        }

        try {
            $photo = (string) $existing['photo'];
            $uploaded = store_uploaded_image($_FILES['editPhoto'] ?? [], 'views/img/users', $username);
            if ($uploaded !== null) {
                safe_managed_file_delete($photo, 'views/img/users');
                $photo = $uploaded;
            }
            $result = UsersModel::mdlEditUser('users', [
                'name' => $name,
                'user' => $username,
                'password' => $password === '' ? null : password_hash($password, PASSWORD_DEFAULT),
                'profile' => $profile,
                'photo' => $photo,
            ]);
            if ($result === 'ok') {
                audit_event('user.updated', 'user', $username, ['profile' => $profile]);
            }
            ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'User updated successfully.' : 'The user could not be updated.', 'users');
        } catch (Throwable $error) {
            ui_alert('error', app_config('debug') ? $error->getMessage() : 'The user could not be updated.', 'users');
        }
    }

    public static function ctrDeleteUser(): void
    {
        if (!isset($_POST['deleteUserId'])) {
            return;
        }
        require_auth(['Administrator']);
        $id = filter_var($_POST['deleteUserId'], FILTER_VALIDATE_INT);
        if (!$id || $id === (int) $_SESSION['id']) {
            ui_alert('error', 'You cannot delete this user.', 'users');
            return;
        }

        $existing = UsersModel::MdlShowUsers('users', 'id', $id);
        $result = UsersModel::mdlDeleteUser('users', $id);
        if ($result === 'ok') {
            safe_managed_file_delete((string) ($existing['photo'] ?? ''), 'views/img/users');
            audit_event('user.deleted', 'user', $id);
        }
        $message = $result === 'ok'
            ? 'User deleted successfully.'
            : ($result === 'in_use' ? 'Deactivate users who have sales or expense history instead of deleting them.' : 'The user could not be deleted.');
        ui_alert($result === 'ok' ? 'success' : 'error', $message, 'users');
    }
}
