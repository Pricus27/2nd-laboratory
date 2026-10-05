<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_valid(): bool
{
    $sent = $_POST['csrf'] ?? '';
    $stored = $_SESSION['csrf'] ?? '';

    return is_string($sent) && is_string($stored) && $sent !== '' && hash_equals($stored, $sent);
}

function old(string $key, string $default = ''): string
{
    $value = $_SESSION['old'][$key] ?? $default;
    return is_string($value) ? $value : $default;
}

function set_old(array $input): void
{
    $_SESSION['old'] = $input;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_guest(): void
{
    if (current_user() !== null) {
        header('Location: home.php');
        exit;
    }
}

function require_auth(): void
{
    if (current_user() === null) {
        header('Location: login.php');
        exit;
    }
}
