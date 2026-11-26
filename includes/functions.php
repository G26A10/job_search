<?php
// Общие вспомогательные функции.

/**
 * Экранирует текст для безопасного вывода в HTML (защита от XSS).
 *
 * @param mixed $text исходный текст
 * @return string безопасная строка
 */
function e($text)
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

/**
 * Перенаправляет на другую страницу и завершает скрипт.
 *
 * @param string $url адрес страницы
 * @return void
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Сохраняет сообщение, которое будет показано один раз.
 *
 * @param string $type тип: success или error
 * @param string $text текст сообщения
 * @return void
 */
function setFlash($type, $text)
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}

/**
 * Возвращает сообщение и удаляет его из сессии.
 *
 * @return array|null сообщение или null
 */
function getFlash()
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Возвращает CSRF-токен, создавая его при необходимости.
 *
 * @return string токен
 */
function csrfToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Возвращает скрытое поле с CSRF-токеном для формы.
 *
 * @return string HTML поля
 */
function csrfField()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/**
 * Проверяет CSRF-токен из POST-запроса. При ошибке останавливает скрипт.
 *
 * @return void
 */
function checkCsrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Ошибка безопасности: неверный CSRF-токен.');
    }
}

/**
 * Создает новый пример для CAPTCHA и запоминает ответ в сессии.
 *
 * @return string текст вопроса
 */
function makeCaptcha()
{
    $a = random_int(1, 9);
    $b = random_int(1, 9);

    if (random_int(0, 1) === 1) {
        $_SESSION['captcha_answer'] = $a + $b;
        return "Сколько будет $a + $b?";
    }

    // Для вычитания берем большее число первым, чтобы ответ не был отрицательным
    $big = max($a, $b);
    $small = min($a, $b);
    $_SESSION['captcha_answer'] = $big - $small;
    return "Сколько будет $big - $small?";
}

/**
 * Проверяет ответ на CAPTCHA на стороне сервера.
 * После проверки ответ удаляется, поэтому его нельзя использовать повторно.
 *
 * @param string $answer ответ пользователя
 * @return bool true, если ответ верный
 */
function checkCaptcha($answer)
{
    $right = $_SESSION['captcha_answer'] ?? null;
    unset($_SESSION['captcha_answer']);

    $answer = trim($answer);
    if ($right === null || !ctype_digit($answer)) {
        return false;
    }
    return (int)$answer === (int)$right;
}

/**
 * Возвращает название типа занятости по ключу.
 *
 * @param string $key ключ (full, part, remote)
 * @return string название
 */
function employmentLabel($key)
{
    global $employmentTypes;
    return $employmentTypes[$key] ?? $key;
}

/**
 * Форматирует дату для вывода.
 *
 * @param string $date дата в формате Y-m-d H:i:s
 * @return string дата в формате дд.мм.гггг
 */
function formatDate($date)
{
    return date('d.m.Y', strtotime($date));
}

/**
 * Проверяет данные вакансии.
 *
 * @param array $data данные формы
 * @return array список ошибок (пустой, если все верно)
 */
function validateJob($data)
{
    global $employmentTypes;
    $errors = [];

    if ($data['title'] === '' || mb_strlen($data['title']) > 100) {
        $errors['title'] = 'Введите название вакансии (до 100 символов).';
    }
    if ($data['company'] === '' || mb_strlen($data['company']) > 100) {
        $errors['company'] = 'Введите название компании (до 100 символов).';
    }
    if ($data['city'] === '' || mb_strlen($data['city']) > 50) {
        $errors['city'] = 'Введите город (до 50 символов).';
    }
    if (!ctype_digit($data['salary']) || (int)$data['salary'] < 1 || (int)$data['salary'] > 10000000) {
        $errors['salary'] = 'Зарплата должна быть целым числом от 1 до 10000000.';
    }
    if (!isset($employmentTypes[$data['employment_type']])) {
        $errors['employment_type'] = 'Выберите тип занятости из списка.';
    }
    if (mb_strlen($data['description']) < 10 || mb_strlen($data['description']) > 2000) {
        $errors['description'] = 'Описание должно содержать от 10 до 2000 символов.';
    }
    if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['contact_email'] = 'Введите корректный email.';
    }

    return $errors;
}

/**
 * Берет данные вакансии из POST-запроса и обрезает пробелы.
 *
 * @return array данные вакансии
 */
function getJobDataFromPost()
{
    $fields = ['title', 'company', 'city', 'salary', 'employment_type', 'description', 'contact_email'];
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = trim((string)($_POST[$field] ?? ''));
    }
    return $data;
}

/**
 * Возвращает пустые значения полей вакансии для новой формы.
 *
 * @return array пустая вакансия
 */
function emptyJob()
{
    return [
        'title' => '', 'company' => '', 'city' => '', 'salary' => '',
        'employment_type' => '', 'description' => '', 'contact_email' => '',
    ];
}

/**
 * Ищет вакансию по id.
 *
 * @param int $id id вакансии
 * @return array|null вакансия или null
 */
function findJob($id)
{
    $stmt = getDb()->prepare('SELECT * FROM jobs WHERE id = ?');
    $stmt->execute([$id]);
    $job = $stmt->fetch();
    return $job ? $job : null;
}

/**
 * Проверяет новый пароль и его повтор.
 *
 * @param string $password новый пароль
 * @param string $confirm повтор пароля
 * @return string|null текст ошибки или null
 */
function checkNewPassword($password, $confirm)
{
    if (strlen($password) < 6) {
        return 'Пароль должен содержать минимум 6 символов.';
    }
    if ($password !== $confirm) {
        return 'Пароли не совпадают.';
    }
    return null;
}

/**
 * Выводит карточку вакансии.
 *
 * @param array $job строка из таблицы jobs
 * @param string $base префикс пути ('' или '../')
 * @return void
 */
function showJobCard($job, $base = '')
{
    $shortText = mb_strimwidth($job['description'], 0, 120, '...');
    ?>
    <div class="card">
        <h3><a href="<?= $base ?>job.php?id=<?= (int)$job['id'] ?>"><?= e($job['title']) ?></a></h3>
        <p class="company"><?= e($job['company']) ?></p>
        <p>
            Город: <?= e($job['city']) ?> |
            Зарплата: <?= number_format((int)$job['salary'], 0, '', ' ') ?> |
            <?= e(employmentLabel($job['employment_type'])) ?>
        </p>
        <p><?= e($shortText) ?></p>
        <p class="date">Опубликовано: <?= formatDate($job['created_at']) ?></p>
    </div>
    <?php
}
