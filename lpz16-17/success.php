<?php
session_start();

if (!isset($_SESSION['registered_email'])) {
    header('Location: index.php');
    exit();
}

$name = $_SESSION['registered_name'];
$email = $_SESSION['registered_email'];

unset($_SESSION['registered_email']);
unset($_SESSION['registered_name']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация успешна</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-details { background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: left; }
        .success-details p { margin: 10px 0; font-size: 16px; }
        .regex-summary { background: #e3f2fd; border-left: 4px solid #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-box">
            <h2>Регистрация прошла успешно!</h2>
            <p>Спасибо, <?php echo htmlspecialchars($name); ?>!</p>
            <p>Ваш email: <?php echo htmlspecialchars($email); ?></p>
        </div>

        <div class="success-details">
            <h3>Данные прошли валидацию:</h3>
            <p>Имя — только буквы, пробелы, дефисы</p>
            <p>Email — корректный формат</p>
            <p>Телефон — формат +7 XXX XXX-XX-XX</p>
            <p>Пароль — минимум 8 символов, оба регистра, цифры, спецсимволы</p>
            <p>Дата рождения — формат ДД.ММ.ГГГГ, возраст 18+</p>
            <p>Индекс — 6 цифр</p>
            <p>Город — только буквы, пробелы, дефисы</p>
            <p>СНИЛС — формат XXX-XXX-XXX XX</p>
        </div>

        <div class="info-box regex-summary">
            <h4>Использованные регулярные выражения:</h4>
            <p><code>$pattern_name = '/^[a-zA-Zа-яА-Я\s-]{2,30}$/u';</code></p>
            <p><code>$pattern_email = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';</code></p>
            <p><code>$pattern_phone = '/^\+7\s\d{3}\s\d{3}-\d{2}-\d{2}$/';</code></p>
            <p><code>$pattern_password = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';</code></p>
            <p><code>$pattern_birthdate = '/^\d{2}\.\d{2}\.\d{4}$/';</code></p>
            <p><code>$pattern_zipcode = '/^\d{6}$/';</code></p>
            <p><code>$pattern_city = '/^[a-zA-Zа-яА-Я\s-]{2,50}$/u';</code></p>
            <p><code>$pattern_snils = '/^\d{3}-\d{3}-\d{3} \d{2}$/';</code></p>
        </div>

        <a href="index.php" class="back-link">← Зарегистрировать нового пользователя</a>
    </div>
</body>
</html>