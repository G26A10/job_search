<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$users = getDb()->query('SELECT * FROM users ORDER BY id')->fetchAll();

$base = '../';
$pageTitle = 'Пользователи';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/admin_menu.php';
?>
<h1>Пользователи</h1>
<table>
    <tr><th>ID</th><th>Имя</th><th>Email</th><th>Роль</th><th>Дата регистрации</th><th>Действия</th></tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= (int)$user['id'] ?></td>
            <td><?= e($user['name']) ?></td>
            <td><?= e($user['email']) ?></td>
            <td><?= e($user['role']) ?></td>
            <td><?= formatDate($user['created_at']) ?></td>
            <td>
                <?php if ((int)$user['id'] === currentUserId()): ?>
                    это вы
                <?php else: ?>
                    <form method="post" action="delete_user.php" class="confirm-delete">
                        <?= csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                        <button type="submit" class="danger">Удалить</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
