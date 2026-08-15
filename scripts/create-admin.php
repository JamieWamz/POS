<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/core/bootstrap.php';
require_once dirname(__DIR__) . '/models/connection.php';

function option_value(string $name): ?string
{
    $options = getopt('', [$name . ':']);
    $value = $options[$name] ?? null;
    return is_string($value) ? trim($value) : null;
}

function prompt_value(string $label): string
{
    fwrite(STDOUT, $label . ': ');
    return trim((string) fgets(STDIN));
}

function prompt_password(): string
{
    $environmentPassword = getenv('POS_ADMIN_PASSWORD');
    if ($environmentPassword !== false) {
        return (string) $environmentPassword;
    }

    fwrite(STDOUT, 'Password: ');
    $canHide = DIRECTORY_SEPARATOR === '/' && function_exists('shell_exec') && trim((string) shell_exec('command -v stty 2>/dev/null')) !== '';
    if ($canHide) {
        shell_exec('stty -echo');
    }
    $password = rtrim((string) fgets(STDIN), "\r\n");
    if ($canHide) {
        shell_exec('stty echo');
        fwrite(STDOUT, PHP_EOL);
    }
    return $password;
}

$username = option_value('username') ?: prompt_value('Username');
$name = option_value('name') ?: prompt_value('Display name');
$password = prompt_password();

if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
    fwrite(STDERR, "Username must be 3-50 letters, numbers, dots, dashes, or underscores.\n");
    exit(1);
}
if (!preg_match('/^[\p{L}\p{N} .\'-]{2,100}$/u', $name)) {
    fwrite(STDERR, "Display name must be 2-100 characters.\n");
    exit(1);
}
if (strlen($password) < 12) {
    fwrite(STDERR, "Password must contain at least 12 characters.\n");
    exit(1);
}

try {
    $statement = Connection::connect()->prepare(
        "INSERT INTO users(name, user, password, profile, photo, status) VALUES (:name, :user, :password, 'Administrator', '', 1)"
    );
    $statement->execute([
        'name' => $name,
        'user' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
    fwrite(STDOUT, "Administrator created successfully.\n");
} catch (PDOException $error) {
    $message = $error->getCode() === '23000' ? 'That username already exists.' : 'Could not create the administrator. Check the database configuration.';
    fwrite(STDERR, $message . PHP_EOL);
    if (app_config('debug')) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL);
    }
    exit(1);
}
