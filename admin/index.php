<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$usersCount = getDb()->query('SELECT COUNT(*) FROM users')->fetchColumn();
$adminsCount = getDb()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
$jobsCount = getDb()->query('SELECT COUNT(*) FROM jobs')->fetchColumn();

$base = '../';
$pageTitle = 'Админ-панель';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/admin_menu.php';
?>
<h1>Административная панель</h1>
<table>
    <tr><th>Пользователей всего</th><td><?= (int)$usersCount ?></td></tr>
    <tr><th>Из них администраторов</th><td><?= (int)$adminsCount ?></td></tr>
    <tr><th>Вакансий всего</th><td><?= (int)$jobsCount ?></td></tr>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>
