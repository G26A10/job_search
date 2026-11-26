<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$sent = false;
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $email = trim($_POST['email'] ?? '');
    $sent = true;

    $stmt = getDb()->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + RESET_MINUTES * 60);

        // Старые токены этого пользователя удаляем
        $stmt = getDb()->prepare('DELETE FROM password_resets WHERE user_id = ?');
        $stmt->execute([$user['id']]);

        $stmt = getDb()->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $tokenHash, $expires]);

        $resetLink = 'reset_password.php?token=' . $token;
    }
}

$pageTitle = 'Восстановление пароля';
require __DIR__ . '/includes/header.php';
?>
<h1>Восстановление пароля</h1>
<?php if ($sent): ?>
    <div class="message info">Если есть такой аккаунт сейчас вот снизу ссылочка кликай.</div>
    <?php if (DEMO_MODE && $resetLink !== ''): ?>
        <div class="message info">
            <a href="<?= e($resetLink) ?>"><?= e($resetLink) ?></a>
        </div>
    <?php endif; ?>
<?php endif; ?>
<form method="post">
    <?= csrfField() ?>
    <label for="email">Электронная почта</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Получить ссылку</button>
</form>
<p><a href="login.php">Вернуться ко входу</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
