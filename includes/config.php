<?php
session_start();
define('DB_PATH', __DIR__ . '/../database/database.sqlite');
define('DEMO_MODE', true);
define('RESET_MINUTES', 30);
$employmentTypes = [
    'full'   => 'Полная занятость',
    'part'   => 'Частичная занятость',
    'remote' => 'Удаленная работа',
];
