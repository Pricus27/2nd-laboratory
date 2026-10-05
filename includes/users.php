<?php

declare(strict_types=1);

function users_path(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'users.json';
}

function load_users(): array
{
    $path = users_path();
    if (!is_file($path)) {
        return [];
    }

    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function save_users(array $users): bool
{
    $dir = dirname(users_path());
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $handle = fopen(users_path(), 'c+');
    if ($handle === false) {
        return false;
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            return false;
        }

        ftruncate($handle, 0);
        rewind($handle);
        $written = fwrite($handle, json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        fflush($handle);
        flock($handle, LOCK_UN);

        return $written !== false;
    } finally {
        fclose($handle);
    }
}

function find_user_by_email(string $email): ?array
{
    $needle = strtolower(trim($email));
    foreach (load_users() as $user) {
        if (isset($user['email']) && strtolower((string) $user['email']) === $needle) {
            return $user;
        }
    }

    return null;
}

function normalize_phone(string $phone): string
{
    $compact = preg_replace('/[\s\-().]/', '', trim($phone)) ?? '';
    if (str_starts_with($compact, '09') && strlen($compact) === 11) {
        return '+63' . substr($compact, 1);
    }
    if (str_starts_with($compact, '63') && strlen($compact) === 12) {
        return '+' . $compact;
    }
    if (str_starts_with($compact, '+63') && strlen($compact) === 13) {
        return $compact;
    }

    return $compact;
}

function valid_phone(string $phone): bool
{
    return (bool) preg_match('/^\+639\d{9}$/', normalize_phone($phone));
}

function valid_fullname(string $name): bool
{
    $name = trim($name);
    $length = mb_strlen($name);
    if ($length < 2 || $length > 80) {
        return false;
    }

    return (bool) preg_match("/^[\p{L}][\p{L}\s.'-]*$/u", $name);
}

function valid_password(string $password): bool
{
    if (strlen($password) < 8 || strlen($password) > 72) {
        return false;
    }

    return (bool) preg_match('/[A-Za-z]/', $password) && (bool) preg_match('/\d/', $password);
}

function register_user(string $fullname, string $email, string $phone, string $password): bool
{
    $users = load_users();
    $users[] = [
        'id' => bin2hex(random_bytes(8)),
        'fullname' => trim($fullname),
        'email' => strtolower(trim($email)),
        'phone' => normalize_phone($phone),
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('c'),
    ];

    return save_users($users);
}
