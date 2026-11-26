<?php
// Функции для проверки входа и роли пользователя.

/**
 * Проверяет, вошел ли пользователь в систему.
 *
 * @return bool true, если пользователь авторизован
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Проверяет, является ли пользователь администратором (по данным сессии).
 *
 * @return bool true, если роль admin
 */
function isAdmin()
{
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

/**
 * Возвращает id текущего пользователя или 0.
 *
 * @return int id пользователя
 */
function currentUserId()
{
    return isLoggedIn() ? (int)$_SESSION['user_id'] : 0;
}

/**
 * Не пускает на страницу неавторизованных пользователей.
 *
 * @param string $base префикс пути к корню сайта ('' или '../')
 * @return void
 */
function requireLogin($base = '')
{
    if (!isLoggedIn()) {
        setFlash('error', 'Сначала войдите в аккаунт.');
        redirect($base . 'login.php');
    }
}

/**
 * Не пускает на страницу всех, кроме администраторов.
 * Роль берется из базы, чтобы изменения прав применялись сразу.
 *
 * @return void
 */
function requireAdmin()
{
    requireLogin('../');

    $stmt = getDb()->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([currentUserId()]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] !== 'admin') {
        setFlash('error', 'Доступ только для администратора.');
        redirect('../index.php');
    }
}

/**
 * Записывает данные пользователя в сессию после входа.
 *
 * @param array $user строка из таблицы users
 * @return void
 */
function loginUser($user)
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
}
