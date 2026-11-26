<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}
checkCsrf();

$id = (int)($_POST['id'] ?? 0);

if ($id === currentUserId()) {
    setFlash('error', 'Нельзя удалить собственный аккаунт.');
    redirect('users.php');
}

// Вакансии и токены пользователя удаляются автоматически (ON DELETE CASCADE)
$stmt = getDb()->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);

setFlash('success', 'Пользователь удален вместе с его вакансиями.');
redirect('users.php');
