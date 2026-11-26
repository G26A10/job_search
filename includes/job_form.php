<?php
/** @var array $job значения полей вакансии */
/** @var array $errors ошибки проверки по полям */
/** @var string $formButton текст кнопки */
/** @var array $employmentTypes типы занятости из config.php */
?>
<form method="post" id="job-form" novalidate>
    <?= csrfField() ?>

    <label for="title">Название вакансии</label>
    <input type="text" id="title" name="title" maxlength="100" required value="<?= e($job['title']) ?>">
    <span class="error" data-for="title"><?= e($errors['title'] ?? '') ?></span>

    <label for="company">Название компании</label>
    <input type="text" id="company" name="company" maxlength="100" required value="<?= e($job['company']) ?>">
    <span class="error" data-for="company"><?= e($errors['company'] ?? '') ?></span>

    <label for="city">Город</label>
    <input type="text" id="city" name="city" maxlength="50" required value="<?= e($job['city']) ?>">
    <span class="error" data-for="city"><?= e($errors['city'] ?? '') ?></span>

    <label for="salary">Зарплата</label>
    <input type="number" id="salary" name="salary" min="1" max="10000000" required value="<?= e($job['salary']) ?>">
    <span class="error" data-for="salary"><?= e($errors['salary'] ?? '') ?></span>

    <label for="employment_type">Тип занятости</label>
    <select id="employment_type" name="employment_type" required>
        <option value="">Выберите...</option>
        <?php foreach ($employmentTypes as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $job['employment_type'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <span class="error" data-for="employment_type"><?= e($errors['employment_type'] ?? '') ?></span>

    <label for="description">Описание вакансии</label>
    <textarea id="description" name="description" rows="6" maxlength="2000" required><?= e($job['description']) ?></textarea>
    <span class="error" data-for="description"><?= e($errors['description'] ?? '') ?></span>

    <label for="contact_email">Контактный email</label>
    <input type="email" id="contact_email" name="contact_email" required value="<?= e($job['contact_email']) ?>">
    <span class="error" data-for="contact_email"><?= e($errors['contact_email'] ?? '') ?></span>

    <button type="submit"><?= e($formButton) ?></button>
</form>
