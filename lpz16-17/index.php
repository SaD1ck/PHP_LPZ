<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_POST['submitted'])) {
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
}
$form_data = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['form_data']);
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Форма регистрации</h1>
        <p class="subtitle">Все поля проверяются регулярными выражениями</p>

        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <h3>Ошибки в форме:</h3>
            <ul>
                <?php foreach ($errors as $field => $error): ?>
                <li><strong><?php echo $field; ?>:</strong> <?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="registration-form">
            <div class="form-group">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" placeholder="Иван Петров"
                    value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required>
                <small class="hint">Только буквы, пробелы, дефисы. От 2 до 30 символов</small>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="ivan@example.com"
                    value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                <small class="hint">example@domain.com</small>
            </div>

            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" placeholder="+7 999 123-45-67"
                    value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" required>
                <small class="hint">Формат: +7 999 123-45-67</small>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" placeholder="Введите пароль" required>
                <small class="hint">Минимум 8 символов, буквы и цифры</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Подтверждение пароля:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Повторите пароль" required>
            </div>

            <div class="form-group">
                <label for="birthdate">Дата рождения:</label>
                <input type="text" id="birthdate" name="birthdate" placeholder="15.05.1990"
                    value="<?php echo htmlspecialchars($form_data['birthdate'] ?? ''); ?>" required>
                <small class="hint">Формат: ДД.ММ.ГГГГ (например, 15.05.1990)</small>
            </div>

            <div class="form-group">
                <label for="zipcode">Почтовый индекс:</label>
                <input type="text" id="zipcode" name="zipcode" placeholder="123456"
                    value="<?php echo htmlspecialchars($form_data['zipcode'] ?? ''); ?>" required>
                <small class="hint">6 цифр</small>
            </div>

            <div class="form-group">
                <label for="city">Город:</label>
                <input type="text" id="city" name="city" placeholder="Москва"
                    value="<?php echo htmlspecialchars($form_data['city'] ?? ''); ?>" required>
                <small class="hint">Только буквы, пробелы, дефисы. От 2 до 50 символов</small>
            </div>

            <div class="form-group">
                <label for="snils">СНИЛС:</label>
                <input type="text" id="snils" name="snils" placeholder="123-456-789 00"
                    value="<?php echo htmlspecialchars($form_data['snils'] ?? ''); ?>" required>
                <small class="hint">Формат: XXX-XXX-XXX XX</small>
            </div>

            <input type="hidden" name="submitted" value="1">
            <button type="submit" class="btn">Зарегистрироваться</button>
        </form>

        <div class="info-box">
            <h4>Примеры регулярных выражений:</h4>
            <p><code>Имя: /^[a-zA-Zа-яА-Я\s-]{2,30}$/u</code></p>
            <p><code>Email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/</code></p>
            <p><code>Телефон: /^\+7\s\d{3}\s\d{3}-\d{2}-\d{2}$/</code></p>
            <p><code>Пароль: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/</code></p>
            <p><code>Дата: /^\d{2}\.\d{2}\.\d{4}$/</code></p>
            <p><code>Индекс: /^\d{6}$/</code></p>
            <p><code>Город: /^[a-zA-Zа-яА-Я\s-]{2,50}$/u</code></p>
            <p><code>СНИЛС: /^\d{3}-\d{3}-\d{3} \d{2}$/</code></p>
        </div>
    </div>
</body>
</html>