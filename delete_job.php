<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('profile.php');
}
checkCsrf();

$id = (int)($_POST['id'] ?? 0);


$stmt = getDb()->prepare('DELETE FROM jobs WHERE id = ? AND user_id = ?');
$stmt->execute([$id, currentUserId()]);

if ($stmt->rowCount() > 0) {
    setFlash('success', 'Вакансия удалена.');
} else {
    setFlash('error', 'Вакансия не найдена или вам не принадлежит.');
}
redirect('profile.php');
