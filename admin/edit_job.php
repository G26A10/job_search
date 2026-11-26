<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$job = findJob($id);
if (!$job) {
    setFlash('error', 'Вакансия не найдена.');
    redirect('jobs.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $job = getJobDataFromPost();
    $errors = validateJob($job);

    if (count($errors) === 0) {
        $sql = 'UPDATE jobs SET title = ?, company = ?, city = ?, salary = ?, employment_type = ?,
                description = ?, contact_email = ? WHERE id = ?';
        $stmt = getDb()->prepare($sql);
        $stmt->execute([$job['title'], $job['company'], $job['city'], (int)$job['salary'], $job['employment_type'],
            $job['description'], $job['contact_email'], $id]);
        setFlash('success', 'Вакансия обновлена.');
        redirect('jobs.php');
    }
}

$formButton = 'Сохранить';
$base = '../';
$pageTitle = 'Редактирование вакансии';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/admin_menu.php';
?>
<h1>Редактирование вакансии (админ)</h1>
<?php if (count($errors) > 0): ?>
    <div class="message error">Исправьте ошибки в форме.</div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/job_form.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
