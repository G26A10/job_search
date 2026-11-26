<?php
// Работа с базой данных SQLite через PDO.

/**
 * Возвращает подключение к базе данных.
 * При первом вызове создает таблицы и начальные данные.
 *
 * @return PDO подключение к SQLite
 */
function getDb()
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    createTables($pdo);
    addStartData($pdo);

    return $pdo;
}

/**
 * Создает таблицы, если их еще нет.
 *
 * @param PDO $pdo подключение к базе
 * @return void
 */
function createTables($pdo)
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'user',
        created_at TEXT NOT NULL
    )");

    $pdo->exec('CREATE TABLE IF NOT EXISTS jobs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        company TEXT NOT NULL,
        city TEXT NOT NULL,
        salary INTEGER NOT NULL,
        employment_type TEXT NOT NULL,
        description TEXT NOT NULL,
        contact_email TEXT NOT NULL,
        created_at TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS password_resets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        token TEXT NOT NULL,
        expires_at TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )');
}

/**
 * Создает администратора, если в базе нет ни одного.
 *
 * @param PDO $pdo подключение к базе
 * @return void
 */
function addStartData($pdo)
{
    $count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($count > 0) {
        return;
    }

    $hash = password_hash('Admin123', PASSWORD_DEFAULT);

    // Если пользователь с таким email уже есть, делаем его администратором
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute(['admin@example.com']);

    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE users SET role = 'admin', password = ? WHERE email = ?");
        $stmt->execute([$hash, 'admin@example.com']);
    } else {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(['Администратор', 'admin@example.com', $hash, 'admin', date('Y-m-d H:i:s')]);
    }
}
