// Простая клиентская проверка формы вакансии и подтверждение удаления.

var jobForm = document.getElementById('job-form');

if (jobForm) {
    jobForm.addEventListener('submit', function (event) {
        var hasErrors = false;

        // Показывает или очищает ошибку для поля
        function setError(field, text) {
            var span = jobForm.querySelector('span.error[data-for="' + field + '"]');
            span.textContent = text;
            if (text !== '') {
                hasErrors = true;
            }
        }

        var salary = jobForm.elements['salary'].value;
        var email = jobForm.elements['contact_email'].value;

        setError('title', jobForm.elements['title'].value.trim() === '' ? 'Введите название вакансии.' : '');
        setError('company', jobForm.elements['company'].value.trim() === '' ? 'Введите название компании.' : '');
        setError('city', jobForm.elements['city'].value.trim() === '' ? 'Введите город.' : '');
        setError('salary', (!/^[0-9]+$/.test(salary) || Number(salary) < 1) ? 'Зарплата должна быть положительным числом.' : '');
        setError('employment_type', jobForm.elements['employment_type'].value === '' ? 'Выберите тип занятости.' : '');
        setError('description', jobForm.elements['description'].value.trim().length < 10 ? 'Описание: минимум 10 символов.' : '');
        setError('contact_email', !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email) ? 'Введите корректный email.' : '');

        if (hasErrors) {
            event.preventDefault();
        }
    });
}

// Окно подтверждения для форм удаления
var deleteForms = document.querySelectorAll('form.confirm-delete');
for (var i = 0; i < deleteForms.length; i++) {
    deleteForms[i].addEventListener('submit', function (event) {
        if (!confirm('Вы уверены, что хотите удалить?')) {
            event.preventDefault();
        }
    });
}
