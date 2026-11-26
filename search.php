<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$title = trim($_GET['title'] ?? '');
$company = trim($_GET['company'] ?? '');
$city = trim($_GET['city'] ?? '');
$type = trim($_GET['employment_type'] ?? '');

// Строим запрос из условий. Значения передаются только через ?.
$sql = 'SELECT * FROM jobs WHERE 1 = 1';
$params = [];

if ($title !== '') {
    $sql .= ' AND title LIKE ?';
    $params[] = '%' . $title . '%';
}
if ($company !== '') {
    $sql .= ' AND company LIKE ?';
    $params[] = '%' . $company . '%';
}
if ($city !== '') {
    $sql .= ' AND city LIKE ?';
    $params[] = '%' . $city . '%';
}
if (isset($employmentTypes[$type])) {
    $sql .= ' AND employment_type = ?';
    $params[] = $type;
}
$sql .= ' ORDER BY created_at DESC, id DESC';

$stmt = getDb()->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();

$pageTitle = 'Поиск вакансий';
require __DIR__ . '/includes/header.php';
?>
<h1>Поиск вакансий</h1>
<form method="get" class="wide">
    <div class="search-row">
        <div>
            <label for="title">Должность</label>
            <input type="text" id="title" name="title" value="<?= e($title) ?>">
        </div>
        <div>
            <label for="company">Компания</label>
            <input type="text" id="company" name="company" value="<?= e($company) ?>">
        </div>
        <div>
            <label for="city">Город</label>
            <input type="text" id="city" name="city" value="<?= e($city) ?>">
        </div>
        <div>
            <label for="employment_type">Тип занятости</label>
            <select id="employment_type" name="employment_type">
                <option value="">Любой</option>
                <?php foreach ($employmentTypes as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $type === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button type="submit">Найти</button>
    <a class="button gray" href="search.php">Сбросить</a>
</form>

<h2>Найдено вакансий: <?= count($jobs) ?></h2>
<?php foreach ($jobs as $job): ?>
    <?php showJobCard($job); ?>
<?php endforeach; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
