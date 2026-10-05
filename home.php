<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/layout.php';

require_auth();
$user = current_user();

render_head('Home');
?>
<body>
<main class="stage">
    <section class="card home-card" aria-labelledby="home-title">
        <h1 class="form-title" id="home-title">Welcome, <?= e($user['fullname'] ?? 'player') ?></h1>
        <p>You are signed in as <?= e($user['email'] ?? '') ?>.</p>
        <div class="home-actions">
            <a class="btn btn-ghost" href="logout.php">Log out</a>
        </div>
    </section>
    <div class="eclipse eclipse-bottom"></div>
</main>
</body>
</html>
