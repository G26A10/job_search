<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$stmt = getDb()->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([currentUserId()]);
$user = $stmt->fetch();

$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!password_verify($oldPassword, $user['password'])) {
        $passwordError = 'Текущий пароль указан неверно.';
    } else {
        $result = checkNewPassword($newPassword, $confirm);
        if ($result !== null) {
            $passwordError = $result;
        } else {
            $stmt = getDb()->prepare('UPDATE users SET password = ? WHERE id = ?');
            $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $user['id']]);
            setFlash('success', 'Пароль успешно изменен.');
            redirect('profile.php');
        }
    }
}

$stmt = getDb()->prepare('SELECT * FROM jobs WHERE user_id = ? ORDER BY created_at DESC, id DESC');
$stmt->execute([$user['id']]);
$jobs = $stmt->fetchAll();

$pageTitle = 'Личный кабинет';
require __DIR__ . '/includes/header.php';
?>
<h1>Личный кабинет</h1>
<div class="card">
    <p>Имя: <?= e($user['name']) ?></p>
    <p>Email: <?= e($user['email']) ?></p>
    <p>Роль: <?= e($user['role']) ?></p>
    <p>Дата регистрации: <?= formatDate($user['created_at']) ?></p>
</div>

<h2>Мои вакансии</h2>
<?php if (count($jobs) === 0): ?>
    <p>У вас пока нет вакансий. <a href="create_job.php">Создать вакансию</a></p>
<?php else: ?>
    <table>
        <tr><th>Название</th><th>Компания</th><th>Город</th><th>Дата</th><th>Действия</th></tr>
        <?php foreach ($jobs as $job): ?>
            <tr>
                <td><a href="job.php?id=<?= (int)$job['id'] ?>"><?= e($job['title']) ?></a></td>
                <td><?= e($job['company']) ?></td>
                <td><?= e($job['city']) ?></td>
                <td><?= formatDate($job['created_at']) ?></td>
                <td>
                    <a class="button" href="edit_job.php?id=<?= (int)$job['id'] ?>">Изменить</a>
                    <form method="post" action="delete_job.php" class="confirm-delete">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$job['id'] ?>">
                        <button type="submit" class="danger">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h2>Смена пароля</h2>
<?php if ($passwordError !== ''): ?>
    <div class="message error"><?= e($passwordError) ?></div>
<?php endif; ?>
<form method="post">
    <?= csrfField() ?>
    <label for="old_password">Текущий пароль</label>
    <input type="password" id="old_password" name="old_password" required>
    <label for="new_password">Новый пароль</label>
    <input type="password" id="new_password" name="new_password" minlength="6" required>
    <label for="confirm">Повторите новый пароль</label>
    <input type="password" id="confirm" name="confirm" minlength="6" required>
    <button type="submit">Изменить пароль</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
