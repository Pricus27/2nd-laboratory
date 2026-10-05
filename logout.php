<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$_SESSION = [];
session_destroy();
session_start();
session_regenerate_id(true);
flash('success', 'You have been logged out.');
header('Location: login.php');
exit;
