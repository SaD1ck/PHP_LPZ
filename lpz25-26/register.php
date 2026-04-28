<?php
require_once 'config.php';

// Если уже авторизован, перенаправляем в личный кабинет
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error   = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name        = trim($_POST['full_name'] ?? '');

    // Валидация всех полей
    $error_username  = validateUsername($username);
    $error_email     = validateEmail($email);
    $error_password  = validatePassword($password);
    $error_full_name = validateFullName($full_name);

    if ($error_username) {
        $error = $error_username;
    } elseif ($error_email) {
        $error = $error_email;
    } elseif ($error_password) {
        $error = $error_password;
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif ($error_full_name) {
        $error = $error_full_name;
    } else {
        $result = registerUser($username, $email, $password, $full_name);

        if ($result['success']) {
            $success = $result['message'];
            // Очищаем форму после успешной регистрации
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
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">🔐 AuthSystem</a></div>
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
                            <input type="text" name="username" required
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            <small style="color: #888;">Только буквы, цифры и _, от 3 до 50 символов</small>
                        </div>

                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label>Пароль *</label>
                            <input type="password" name="password" required>
                            <small style="color: #888;">Минимум 6 символов</small>
                        </div>

                        <div class="form-group">
                            <label>Подтверждение пароля *</label>
                            <input type="password" name="confirm_password" required>
                        </div>

                        <div class="form-group">
                            <label>Полное имя (необязательно)</label>
                            <input type="text" name="full_name"
                                   value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
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
            <p>Система аутентификации и авторизации &copy; 2025</p>
        </div>
    </div>
</body>
</html>