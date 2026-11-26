<?php
// Шапка сайта. Перед подключением можно задать $pageTitle и $base.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

if (!isset($base)) {
    $base = '';
}
if (!isset($pageTitle)) {
    $pageTitle = 'Главная';
}
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle) ?> - Работа Рядом</title>
    <link rel="stylesheet" href="<?= $base ?>assets/style.css">
</head>
<body>
<header>
    <div class="container nav">
        <a class="logo" href="<?= $base ?>index.php">Работа Рядом</a>
        <nav>
            <a href="<?= $base ?>index.php">Главная</a>
            <a href="<?= $base ?>search.php">Поиск</a>
            <?php if (isLoggedIn()): ?>
                <a href="<?= $base ?>create_job.php">Добавить вакансию</a>
                <a href="<?= $base ?>profile.php">Личный кабинет</a>
                <?php if (isAdmin()): ?>
                    <a href="<?= $base ?>admin/index.php">Админ-панель</a>
                <?php endif; ?>
                <a href="<?= $base ?>logout.php">Выйти (<?= e($_SESSION['user_name']) ?>)</a>
            <?php else: ?>
                <a href="<?= $base ?>login.php">Вход</a>
                <a href="<?= $base ?>register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
<?php if ($flash): ?>
    <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
<?php endif; ?>
