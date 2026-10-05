<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/layout.php';

require_guest();

$errors = [];
$notice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    set_old(['email' => $email]);

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } elseif ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } else {
        clear_old();
        $notice = 'If an account exists for that email, password reset instructions have been recorded.';
    }
}

render_head('Forgot Password');
?>
<body>
<main class="stage">
    <?php render_logo('logo logo-top'); ?>
    <section class="card" aria-labelledby="forgot-title">
        <h1 class="form-title" id="forgot-title">Forgot Password</h1>
        <?php render_alerts($notice ? ['type' => 'success', 'message' => $notice] : null, isset($errors['form']) ? [$errors['form']] : []); ?>
        <form method="post" action="forgot.php" data-validate="forgot" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="username@gmail.com" value="<?= e(old('email')) ?>" autocomplete="email">
                <?php field_error($errors, 'email'); ?>
            </div>
            <button class="btn btn-signin" type="submit">Send reset note</button>
            <p class="switch">Remembered it?<a href="login.php">Back to login</a></p>
        </form>
    </section>
    <div class="eclipse eclipse-bottom"></div>
</main>
<script src="assets/js/auth.js"></script>
</body>
</html>
