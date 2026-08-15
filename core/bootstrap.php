<?php

declare(strict_types=1);

function app_load_environment(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = array_map('trim', explode('=', $line, 2));
        if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $name) || getenv($name) !== false) {
            continue;
        }

        if (strlen($value) >= 2 && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
    }
}

function app_bootstrap(): void
{
    static $booted = false;
    if ($booted) {
        return;
    }
    $booted = true;

    $root = dirname(__DIR__);
    app_load_environment($root . '/.env');
    $GLOBALS['app_config'] = require $root . '/config/app.php';

    date_default_timezone_set((string) app_config('timezone', 'Africa/Lusaka'));
    ini_set('display_errors', app_config('debug', false) ? '1' : '0');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name((string) app_config('session_name', 'golden_tap_session'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    if (PHP_SAPI !== 'cli' && !headers_sent()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' https://images.openfoodfacts.org https://static.openfoodfacts.org data: blob:; font-src 'self' data:");
        header('Cache-Control: no-store, private');
    }
}

function app_config(?string $key = null, mixed $default = null): mixed
{
    $config = $GLOBALS['app_config'] ?? [];
    if ($key === null) {
        return $config;
    }

    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_authenticated(): bool
{
    return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === 'ok' && isset($_SESSION['id']);
}

function current_user_role(): ?string
{
    return is_authenticated() ? (string) ($_SESSION['profile'] ?? '') : null;
}

function user_has_role(array $roles): bool
{
    return is_authenticated() && ($roles === [] || in_array(current_user_role(), $roles, true));
}

function request_expects_json(): bool
{
    $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));
    return str_contains($accept, 'application/json') || str_contains((string) ($_SERVER['REQUEST_URI'] ?? ''), '/ajax/');
}

function abort_request(int $status, string $message): never
{
    http_response_code($status);
    if (request_expects_json()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => $message], JSON_UNESCAPED_SLASHES);
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
    }
    exit;
}

function require_auth(array $roles = []): void
{
    if (!is_authenticated()) {
        abort_request(401, 'Authentication required.');
    }
    if ($roles !== [] && !user_has_role($roles)) {
        abort_request(403, 'You do not have permission to perform this action.');
    }
}

function require_method(string $method): void
{
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== strtoupper($method)) {
        header('Allow: ' . strtoupper($method));
        abort_request(405, 'Method not allowed.');
    }
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_is_valid(): bool
{
    $provided = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($provided) && $provided !== '' && hash_equals(csrf_token(), $provided);
}

function require_csrf(): void
{
    if (!csrf_is_valid()) {
        abort_request(419, 'Your session token expired. Refresh the page and try again.');
    }
}

function valid_date(?string $date): ?string
{
    if ($date === null || $date === '') {
        return null;
    }
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $parsed && $parsed->format('Y-m-d') === $date ? $date : null;
}

function safe_managed_file_delete(?string $relativePath, string $allowedRelativeDirectory): bool
{
    if (!$relativePath) {
        return false;
    }

    $root = realpath(dirname(__DIR__));
    $allowed = realpath(dirname(__DIR__) . '/' . trim($allowedRelativeDirectory, '/'));
    $candidate = realpath(dirname(__DIR__) . '/' . ltrim($relativePath, '/'));
    if (!$root || !$allowed || !$candidate || !is_file($candidate)) {
        return false;
    }

    $prefix = rtrim($allowed, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (!str_starts_with($candidate, $prefix)) {
        return false;
    }

    return unlink($candidate);
}

function store_uploaded_image(array $file, string $relativeDirectory, string $bucket): ?string
{
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($error !== UPLOAD_ERR_OK || !isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('The image upload could not be completed.');
    }
    if ((int) ($file['size'] ?? 0) > (int) app_config('uploads.max_bytes', 5242880)) {
        throw new RuntimeException('Images must be 5 MB or smaller.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $allowed = app_config('uploads.allowed_image_mimes', []);
    if (!is_string($mime) || !isset($allowed[$mime]) || @getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Upload a valid JPEG, PNG, or WebP image.');
    }

    $bucket = preg_replace('/[^a-zA-Z0-9_-]/', '', $bucket) ?: 'item';
    $relativeDirectory = trim($relativeDirectory, '/');
    $targetDirectory = dirname(__DIR__) . '/' . $relativeDirectory . '/' . $bucket;
    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0750, true) && !is_dir($targetDirectory)) {
        throw new RuntimeException('The image directory could not be created.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $target = $targetDirectory . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('The uploaded image could not be saved.');
    }

    return $relativeDirectory . '/' . $bucket . '/' . $filename;
}

function ui_alert(string $type, string $message, string $redirect): void
{
    $typeJson = json_encode($type, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $messageJson = json_encode($message, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $redirectJson = json_encode($redirect, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    echo "<script>swal({type:{$typeJson},title:{$messageJson},confirmButtonText:'Close'}).then(function(){window.location={$redirectJson};});</script>";
}

function audit_event(string $action, string $entityType, int|string|null $entityId = null, array $metadata = []): void
{
    require_once dirname(__DIR__) . '/models/audit.model.php';
    ModelAudit::log($action, $entityType, $entityId, $metadata);
}

app_bootstrap();
