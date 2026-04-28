<?php
require_once 'functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']); // Запомнить меня

    if (empty($login) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $result = loginUser($login, $password, $remember);
        if ($result['success']) {
            header('Location: index.php');
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
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="light">
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">📓 Личный блокнот</a></div>
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
                        <input type="text" name="login" required 
                               value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
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
                        Логин: admin / Пароль: password
                    </p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Личный блокнот &copy; 2025</p>
        </div>
    </div>
</body>
</html>