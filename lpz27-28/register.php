<?php
require_once 'functions.php';

// Если уже авторизован, идем на главную
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = null;
$success = null;

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Валидируем каждое поле
    $error_login = validateLogin($login);
    $error_password = validatePassword($password);
    $error_name = validateName($name);
    $error_email = validateEmail($email);

    // Проверяем ошибки в порядке приоритета
    if ($error_login) {
        $error = $error_login;
    } elseif ($error_password) {
        $error = $error_password;
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } elseif ($error_name) {
        $error = $error_name;
    } elseif ($error_email) {
        $error = $error_email;
    } else {
        // Все проверки прошли, регистрируем пользователя
        $result = registerUser($login, $password, $name, $email);
        if ($result['success']) {
            $success = $result['message'];
            $_POST = [];
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="light">
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">📓 Личный блокнот</a></div>
            <div class="nav">
                <a href="login.php">🔑 Вход</a>
                <a href="register.php" style="background: #667eea; color: white;">📝 Регистрация</a>
            </div>
        </div>

        <div class="content">
            <div class="auth-container">
                <h1>📝 Регистрация</h1>

                <?php if ($error): ?>
                    <div class="alert alert-error">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">✅ <?php echo $success; ?></div>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="login.php" class="btn">🔑 Войти</a>
                    </div>
                <?php else: ?>
                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label>Логин *</label>
                            <input type="text" name="login" required 
                                   value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
                            <small>Только буквы, цифры и _, 3-50 символов</small>
                        </div>

                        <div class="form-group">
                            <label>Пароль *</label>
                            <input type="password" name="password" required>
                            <small>Минимум 6 символов</small>
                        </div>

                        <div class="form-group">
                            <label>Подтверждение пароля *</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <div class="form-group">
                            <label>Ваше имя *</label>
                            <input type="text" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            <small>Только буквы, 2-100 символов</small>
                        </div>

                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <button type="submit" class="btn btn-block">Зарегистрироваться</button>
                    </form>

                    <div style="text-align: center; margin-top: 20px;">
                        <p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <p>Личный блокнот &copy; 2025</p>
        </div>
    </div>
</body>
</html>