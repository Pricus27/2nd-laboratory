<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/users.php';
require __DIR__ . '/includes/layout.php';

require_guest();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    set_old(['email' => $email]);

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Please try again.';
    }
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if ($errors === []) {
        $user = find_user_by_email($email);
        if ($user === null || !password_verify($password, (string) ($user['password'] ?? ''))) {
            $errors['form'] = 'Email or password is incorrect.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'email' => $user['email'],
            ];
            clear_old();
            header('Location: home.php');
            exit;
        }
    }
}

$flash = pull_flash();
render_head('Login');
?>
<body class="page-login">
<main class="stage">
    <?php render_logo('logo logo-top'); ?>
    <section class="card" aria-labelledby="login-title">
        <h1 class="form-title" id="login-title">Login</h1>
        <?php render_alerts($flash, isset($errors['form']) ? [$errors['form']] : []); ?>
        <form method="post" action="login.php" data-validate="login" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="username@gmail.com" value="<?= e(old('email')) ?>" autocomplete="email" <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>>
                <?php field_error($errors, 'email'); ?>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <div class="control">
                    <input class="has-toggle" type="password" id="password" name="password" placeholder="Password" autocomplete="current-password" <?= isset($errors['password']) ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?>>
                    <button class="toggle-password" type="button" data-toggle-password="password" aria-label="Show password">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 002.8 2.8"/><path d="M9.9 5.1A10.8 10.8 0 0112 5c5 0 9.3 3.1 11 7-0.6 1.4-1.6 2.7-2.8 3.8M6.1 6.1C4.2 7.4 2.7 9.1 1 12c1.7 3.9 6 7 11 7 1.6 0 3.1-.3 4.5-.9"/></svg>
                    </button>
                </div>
                <?php field_error($errors, 'password'); ?>
            </div>
            <a class="forgot" href="forgot.php">Forgot Password?</a>
            <button class="btn btn-signin" type="submit">Sign in</button>
            <p class="divider">or continue with</p>
            <div class="socials">
                <button class="social" type="button" data-social="google" aria-label="Continue with Google">
                    <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.3-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 16 19 12 24 12c3.1 0 5.8 1.2 8 3.1l5.7-5.7C34.2 6.1 29.4 4 24 4 16.3 4 9.6 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.6-5.2l-6.3-5.3C29.2 35.1 26.7 36 24 36c-5.3 0-9.7-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1 2.9-3.1 5.2-5.9 6.5l6.3 5.3C38.2 37.3 44 32 44 24c0-1.2-.1-2.3-.4-3.5z"/></svg>
                </button>
                <button class="social" type="button" data-social="github" aria-label="Continue with GitHub">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#111"><path d="M12 2C6.5 2 2 6.6 2 12.2c0 4.5 2.9 8.3 6.9 9.6.5.1.7-.2.7-.5v-1.7c-2.8.6-3.4-1.2-3.4-1.2-.4-1.1-1.1-1.4-1.1-1.4-.9-.6.1-.6.1-.6 1 .1 1.5 1 1.5 1 .9 1.6 2.4 1.1 3 .9.1-.7.4-1.1.6-1.4-2.2-.3-4.6-1.1-4.6-5 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .8-.3 2.8 1a9.4 9.4 0 015 0c2-1.3 2.8-1 2.8-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.9-2.3 4.7-4.6 5 .4.3.7.9.7 1.9v2.8c0 .3.2.6.7.5 4-1.3 6.9-5.1 6.9-9.6C22 6.6 17.5 2 12 2z"/></svg>
                </button>
                <button class="social" type="button" data-social="facebook" aria-label="Continue with Facebook">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h2.6l.4-3H13v-2c0-.6.4-1 1-1z"/></svg>
                </button>
            </div>
            <p class="social-note" id="social-note" aria-live="polite"></p>
            <p class="switch">Don’t have an account yet?<a href="register.php">Register for free</a></p>
        </form>
    </section>
    <div class="eclipse eclipse-bottom"></div>
</main>
<script src="assets/js/auth.js"></script>
</body>
</html>
