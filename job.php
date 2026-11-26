<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$job = findJob((int)($_GET['id'] ?? 0));
if (!$job) {
    setFlash('error', 'Вакансия не найдена.');
    redirect('index.php');
}

$pageTitle = $job['title'];
require __DIR__ . '/includes/header.php';
?>
<h1><?= e($job['title']) ?></h1>
<div class="card">
    <p class="company"><?= e($job['company']) ?></p>
    <p>Город: <?= e($job['city']) ?></p>
    <p>Зарплата: <?= number_format((int)$job['salary'], 0, '', ' ') ?></p>
    <p>Тип занятости: <?= e(employmentLabel($job['employment_type'])) ?></p>
    <p>Контакты: <?= e($job['contact_email']) ?></p>
    <p class="date">Опубликовано: <?= formatDate($job['created_at']) ?></p>
    <h3>Описание</h3>
    <p><?= nl2br(e($job['description'])) ?></p>
</div>
<p><a href="index.php">Назад к списку</a></p>
<?php require __DIR__ . '/includes/footer.php'; ?>
