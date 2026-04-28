<?php
require_once 'config.php';

// Если уже авторизован, перенаправляем в личный кабинет
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // Чекбокс "Запомнить меня"

    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $result = loginUser($username, $password, $remember);

        if ($result['success']) {
            // Успешный вход — переходим в личный кабинет
            header('Location: dashboard.php');
            exit();
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
    <title>Вход в систему</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">🔐 AuthSystem</a></div>
            <div class="nav">
                <a href="login.php" style="background: #667eea; color: white;">🔑 Вход</a>
                <a href="register.php">📝 Регистрация</a>
            </div>
        </div>

        <div class="content">
            <div class="auth-container">
                <h1>🔑 Вход в систему</h1>

                <?php if ($error): ?>
                    <div class="alert alert-error">❌ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label>Логин</label>
                        <input type="text" name="username" required
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="remember">
                            Запомнить меня (30 дней)
                        </label>
                    </div>

                    <button type="submit" class="btn btn-block">Войти</button>
                </form>

                <div style="text-align: center; margin-top: 20px;">
                    <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
                    <hr style="margin: 15px 0;">
                    <p style="font-size: 12px; color: #888;">
                        <strong>Тестовые данные:</strong><br>
                        Логин: admin / Пароль: 123456<br>
                        Логин: user1 / Пароль: 123456
                    </p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Система аутентификации и авторизации &copy; 2025</p>
        </div>
    </div>
</body>
</html>