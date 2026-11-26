<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$job = emptyJob();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $job = getJobDataFromPost();
    $errors = validateJob($job);

    if (count($errors) === 0) {
        $sql = 'INSERT INTO jobs (user_id, title, company, city, salary, employment_type, description, contact_email, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = getDb()->prepare($sql);
        $stmt->execute([currentUserId(), $job['title'], $job['company'], $job['city'], (int)$job['salary'],
            $job['employment_type'], $job['description'], $job['contact_email'], date('Y-m-d H:i:s')]);

        setFlash('success', 'Вакансия успешно создана.');
        redirect('profile.php');
    }
}

$formButton = 'Опубликовать';
$pageTitle = 'Новая вакансия';
require __DIR__ . '/includes/header.php';
?>
<h1>Новая вакансия</h1>
<?php if (count($errors) > 0): ?>
    <div class="message error">Исправьте ошибки в форме.</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/job_form.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
