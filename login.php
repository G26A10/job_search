<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!checkCaptcha($_POST['captcha'] ?? '')) {
        $error = 'Неверный ответ на CAPTCHA.';
    } elseif ($email === '' || $password === '') {
        $error = 'Введите email и пароль.';
    } else {
        $stmt = getDb()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            setFlash('success', 'Вы успешно вошли в аккаунт.');
            redirect('index.php');
        }
        // Одно и то же сообщение для неверного email и неверного пароля
        $error = 'Неверный email или пароль.';
    }
}

$question = makeCaptcha();
$pageTitle = 'Вход';
require __DIR__ . '/includes/header.php';
?>
<h1>Вход</h1>
<?php if ($error !== ''): ?>
    <div class="message error"><?= e($error) ?></div>
<?php endif; ?>
<form method="post">
    <?= csrfField() ?>
    <label for="email">Электронная почта</label>
    <input type="email" id="email" name="email" required value="<?= e($email) ?>">

    <label for="password">Пароль</label>
    <input type="password" id="password" name="password" required>

    <div class="captcha">
        <label for="captcha"><?= e($question) ?></label>
        <input type="text" id="captcha" name="captcha" autocomplete="off" required>
    </div>

    <button type="submit">Войти</button>
</form>
<p><a href="forgot_password.php">Забыли пароль?</a> | <a href="register.php">Регистрация</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
