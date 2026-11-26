<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$sql = 'SELECT jobs.*, users.name AS owner_name FROM jobs
        JOIN users ON users.id = jobs.user_id ORDER BY jobs.created_at DESC, jobs.id DESC';
$jobs = getDb()->query($sql)->fetchAll();

$base = '../';
$pageTitle = 'Все вакансии';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/admin_menu.php';
?>
<h1>Все вакансии</h1>
<table>
    <tr><th>ID</th><th>Название</th><th>Компания</th><th>Город</th><th>Зарплата</th><th>Владелец</th><th>Действия</th></tr>
    <?php foreach ($jobs as $job): ?>
        <tr>
            <td><?= (int)$job['id'] ?></td>
            <td><a href="../job.php?id=<?= (int)$job['id'] ?>"><?= e($job['title']) ?></a></td>
            <td><?= e($job['company']) ?></td>
            <td><?= e($job['city']) ?></td>
            <td><?= (int)$job['salary'] ?></td>
            <td><?= e($job['owner_name']) ?></td>
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
<?php require __DIR__ . '/../includes/footer.php'; ?>
