<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

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

    if (count($errors) === 0) {
        $stmt = getDb()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким email уже существует.';
        }
    }

    if (count($errors) === 0) {
        $stmt = getDb()->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'admin', date('Y-m-d H:i:s')]);
        setFlash('success', 'Новый администратор создан.');
        redirect('users.php');
    }
}

$base = '../';
$pageTitle = 'Создание администратора';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/admin_menu.php';
?>
<h1>Создание администратора</h1>
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
    <button type="submit">Создать администратора</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
