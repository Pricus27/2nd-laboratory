<?php

declare(strict_types=1);

function render_head(string $title): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> · VALO GANG</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
    <?php
}

function render_logo(string $class = 'logo'): void
{
    ?>
    <div class="<?= e($class) ?>">
        <img src="assets/images/logo.png" alt="VALO GANG">
    </div>
    <?php
}

function render_alerts(?array $flash, array $errors = []): void
{
    if ($flash) {
        $type = $flash['type'] === 'success' ? 'success' : 'error';
        echo '<div class="banner banner-' . e($type) . '" role="status">' . e($flash['message']) . '</div>';
    }

    if ($errors !== []) {
        echo '<div class="banner banner-error" role="alert"><ul class="error-list">';
        foreach ($errors as $message) {
            echo '<li>' . e($message) . '</li>';
        }
        echo '</ul></div>';
    }
}

function field_error(array $errors, string $key): void
{
    if (!isset($errors[$key])) {
        return;
    }

    echo '<p class="field-error" id="' . e($key) . '-error">' . e($errors[$key]) . '</p>';
}
