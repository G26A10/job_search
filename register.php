<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '' || mb_strlen($name) > 50) {
        $errors[] = 'Введите имя (до 50 символов).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email.';
    }
    $passwordError = checkNewPassword($password, $confirm);
    if ($passwordError !== null) {
        $errors[] = $passwordError;
    }
    if (!checkCaptcha($_POST['captcha'] ?? '')) {
        $errors[] = 'Неверный ответ на CAPTCHA.';
    }

    if (count($errors) === 0) {
        $stmt = getDb()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким email уже существует.';
        }
    }

    if (count($errors) === 0) {
        $stmt = getDb()->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'user', date('Y-m-d H:i:s')]);
        setFlash('success', 'Регистрация прошла успешно. Теперь войдите в аккаунт.');
        redirect('login.php');
    }
}

$question = makeCaptcha();
$pageTitle = 'Регистрация';
require __DIR__ . '/includes/header.php';
?>
<h1>Регистрация</h1>
<?php foreach ($errors as $error): ?>
    <div class="message error"><?= e($error) ?></div>
<?php endforeach; ?>
<form method="post">
    <?= csrfField() ?>
    <label for="name">Имя</label>
    <input type="text" id="name" name="name" maxlength="50" required value="<?= e($name) ?>">

    <label for="email">Электронная почта</label>
    <input type="email" id="email" name="email" required value="<?= e($email) ?>">

    <label for="password">Пароль (минимум 6 символов)</label>
    <input type="password" id="password" name="password" minlength="6" required>

    <label for="confirm">Повторите пароль</label>
    <input type="password" id="confirm" name="confirm" minlength="6" required>

    <div class="captcha">
        <label for="captcha"><?= e($question) ?></label>
        <input type="text" id="captcha" name="captcha" autocomplete="off" required>
    </div>

    <button type="submit">Зарегистрироваться</button>
</form>
<p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
