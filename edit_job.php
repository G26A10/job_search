<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

$id = (int)($_GET['id'] ?? 0);
$oldJob = findJob($id);
if (!$oldJob || (int)$oldJob['user_id'] !== currentUserId()) {
    setFlash('error', 'Вы можете редактировать только свои вакансии.');
    redirect('profile.php');
}

$job = $oldJob;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $job = getJobDataFromPost();
    $errors = validateJob($job);

    if (count($errors) === 0) {
        $sql = 'UPDATE jobs SET title = ?, company = ?, city = ?, salary = ?, employment_type = ?,
                description = ?, contact_email = ? WHERE id = ? AND user_id = ?';
        $stmt = getDb()->prepare($sql);
        $stmt->execute([$job['title'], $job['company'], $job['city'], (int)$job['salary'], $job['employment_type'],
            $job['description'], $job['contact_email'], $id, currentUserId()]);

        setFlash('success', 'Вакансия обновлена.');
        redirect('profile.php');
    }
}

$formButton = 'Сохранить';
$pageTitle = 'Редактирование вакансии';
require __DIR__ . '/includes/header.php';
?>
<h1>Редактирование вакансии</h1>
<?php if (count($errors) > 0): ?>
    <div class="message error">Исправьте ошибки в форме.</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/job_form.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
