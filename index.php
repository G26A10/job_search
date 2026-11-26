<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$jobs = getDb()->query('SELECT * FROM jobs ORDER BY created_at DESC, id DESC LIMIT 6')->fetchAll();

$pageTitle = 'Главная';
require __DIR__ . '/includes/header.php';
?>
<h1>Поиск работы</h1>
<p>Просматривайте свежие вакансии, регистрируйтесь и публикуйте собственные объявления.</p>

<form method="get" action="search.php" class="wide">
    <div class="search-row">
        <div>
            <label for="title">Должность</label>
            <input type="text" id="title" name="title">
        </div>
        <div>
            <label for="city">Город</label>
            <input type="text" id="city" name="city">
        </div>
        <div>
            <button type="submit">Найти</button>
        </div>
    </div>
</form>

<h2>Последние вакансии</h2>
<?php if (count($jobs) === 0): ?>
    <p>Вакансий пока нет.</p>
<?php endif; ?>
<?php foreach ($jobs as $job): ?>
    <?php showJobCard($job); ?>
<?php endforeach; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
