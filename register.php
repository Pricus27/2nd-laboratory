<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/users.php';
require __DIR__ . '/includes/layout.php';

require_guest();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim((string) ($_POST['fullname'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    set_old([
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
    ]);

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Please try again.';
    }
    if ($fullname === '') {
        $errors['fullname'] = 'Full name is required.';
    } elseif (!valid_fullname($fullname)) {
        $errors['fullname'] = 'Enter a real name using letters, spaces, hyphens, or apostrophes.';
    }
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } elseif (find_user_by_email($email) !== null) {
        $errors['email'] = 'An account with this email already exists.';
    }
    if ($phone === '') {
        $errors['phone'] = 'Phone number is required.';
    } elseif (!valid_phone($phone)) {
        $errors['phone'] = 'Use a Philippine mobile number, such as +639123456789 or 09123456789.';
    }
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (!valid_password($password)) {
        $errors['password'] = 'Use at least 8 characters with one letter and one number.';
    }
    if ($confirm === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if ($errors === []) {
        if (!register_user($fullname, $email, $phone, $password)) {
            $errors['form'] = 'We could not save your account. Please try again.';
        } else {
            clear_old();
            flash('success', 'Registration successful. You can sign in now.');
            header('Location: login.php');
            exit;
        }
    }
}

render_head('Registration');
?>
<body class="page-register">
<main class="stage">
    <div class="eclipse eclipse-left"></div>
    <section class="card card-wide" aria-labelledby="register-title">
        <form method="post" action="register.php" data-validate="register" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <h1 class="form-title" id="register-title">Registration</h1>
            <?php render_alerts(null, isset($errors['form']) ? [$errors['form']] : []); ?>
            <div class="field">
                <label for="fullname">Fullname</label>
                <input type="text" id="fullname" name="fullname" placeholder="YOUR FULL NAME" value="<?= e(old('fullname')) ?>" autocomplete="name" <?= isset($errors['fullname']) ? 'aria-invalid="true"' : '' ?>>
                <?php field_error($errors, 'fullname'); ?>
            </div>
            <div class="field">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="email@gmail.com" value="<?= e(old('email')) ?>" autocomplete="email" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
                <?php field_error($errors, 'email'); ?>
            </div>
            <div class="field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="+(63)" value="<?= e(old('phone')) ?>" autocomplete="tel" <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
                <?php field_error($errors, 'phone'); ?>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <div class="control">
                    <input class="has-toggle" type="password" id="password" name="password" placeholder="Password" autocomplete="new-password" <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?>>
                    <button class="toggle-password" type="button" data-toggle-password="password" aria-label="Show password">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 002.8 2.8"/><path d="M9.9 5.1A10.8 10.8 0 0112 5c5 0 9.3 3.1 11 7-0.6 1.4-1.6 2.7-2.8 3.8M6.1 6.1C4.2 7.4 2.7 9.1 1 12c1.7 3.9 6 7 11 7 1.6 0 3.1-.3 4.5-.9"/></svg>
                    </button>
                </div>
                <p class="hint">At least 8 characters, with one letter and one number.</p>
                <?php field_error($errors, 'password'); ?>
            </div>
            <div class="field">
                <label for="confirm_password">Confirm Password</label>
                <div class="control">
                    <input class="has-toggle" type="password" id="confirm_password" name="confirm_password" placeholder="Password" autocomplete="new-password" <?= isset($errors['confirm_password']) ? 'aria-invalid="true"' : '' ?>>
                    <button class="toggle-password" type="button" data-toggle-password="confirm_password" aria-label="Show password">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 002.8 2.8"/><path d="M9.9 5.1A10.8 10.8 0 0112 5c5 0 9.3 3.1 11 7-0.6 1.4-1.6 2.7-2.8 3.8M6.1 6.1C4.2 7.4 2.7 9.1 1 12c1.7 3.9 6 7 11 7 1.6 0 3.1-.3 4.5-.9"/></svg>
                    </button>
                </div>
                <?php field_error($errors, 'confirm_password'); ?>
            </div>
            <button class="btn btn-register" type="submit">Create account</button>
            <p class="switch">Already have an account?<a href="login.php">Sign in</a></p>
        </form>
        <?php render_logo('logo logo-side'); ?>
    </section>
</main>
<script src="assets/js/auth.js"></script>
</body>
</html>
