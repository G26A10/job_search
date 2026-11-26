<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('jobs.php');
}
checkCsrf();

$stmt = getDb()->prepare('DELETE FROM jobs WHERE id = ?');
$stmt->execute([(int)($_POST['id'] ?? 0)]);

setFlash('success', 'Вакансия удалена.');
redirect('jobs.php');
