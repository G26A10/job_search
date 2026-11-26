<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

/**
 * Ищет действующий токен восстановления пароля.
 *
 * @param string $token токен из ссылки
 * @return array|null строка из password_resets или null
 */
function findResetToken($token)
{
    $stmt = getDb()->prepare('SELECT * FROM password_resets WHERE token = ? AND expires_at > ?');
    $stmt->execute([hash('sha256', $token), date('Y-m-d H:i:s')]);
    $row = $stmt->fetch();
    return $row ? $row : null;
}

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$reset = findResetToken((string)$token);
$error = '';

if ($reset && $_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $error = checkNewPassword($_POST['password'] ?? '', $_POST['confirm'] ?? '');

    if ($error === null) {
        $stmt = getDb()->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), $reset['user_id']]);

        // Токен больше нельзя использовать
        $stmt = getDb()->prepare('DELETE FROM password_resets WHERE user_id = ?');
        $stmt->execute([$reset['user_id']]);

        setFlash('success', 'Пароль изменен. Теперь можно войти.');
        redirect('login.php');
    }
}

$pageTitle = 'Новый пароль';
require __DIR__ . '/includes/header.php';
?>
<h1>Новый пароль</h1>
<?php if (!$reset): ?>
    <div class="message error">Ссылка недействительна или срок ее действия истек.</div>
    <p><a href="forgot_password.php">Запросить новую ссылку</a></p>
<?php else: ?>
    <?php if ($error): ?>
        <div class="message error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <label for="password">Новый пароль</label>
        <input type="password" id="password" name="password" minlength="6" required>
        <label for="confirm">Повторите пароль</label>
        <input type="password" id="confirm" name="confirm" minlength="6" required>
        <button type="submit">Сохранить пароль</button>
    </form>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
