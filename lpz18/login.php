<?php
require_once 'functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = null;
$remember = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_login'])) {
    $login = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($login) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        $user = findUserByLogin($login);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_login'] = $login;
            if ($remember) {
                setcookie('remember_login', $login, time() + (86400 * 30), '/');
                setcookie('remember_token', hash('sha256', $login . $user['password']), time() + (86400 * 30), '/');
            }
            header('Location: index.php');
            exit();
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}

if (isset($_COOKIE['remember_login']) && isset($_COOKIE['remember_token'])) {
    $user = findUserByLogin($_COOKIE['remember_login']);
    if ($user) {
        $expected_token = hash('sha256', $user['login'] . $user['password']);
        if ($_COOKIE['remember_token'] === $expected_token) {
            $_SESSION['user_login'] = $user['login'];
            header('Location: index.php');
            exit();
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
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { max-width: 400px; width: 100%; background: white; border-radius: 15px; padding: 35px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { text-align: center; margin-bottom: 10px; color: #333; }
        .subtitle { text-align: center; color: #666; margin-bottom: 25px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; }
        input:focus { border-color: #667eea; outline: none; }
        .checkbox { display: flex; align-items: center; gap: 10px; }
        .checkbox input { width: auto; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: bold; }
        button:hover { opacity: 0.9; transform: translateY(-2px); }
        .error { background: #fee; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #c33; }
        .link { text-align: center; margin-top: 20px; }
        .link a { color: #667eea; text-decoration: none; }
        hr { margin: 20px 0; border: none; border-top: 1px solid #eee; }
        .test-data { background: #f5f5f5; padding: 12px; border-radius: 8px; margin-top: 20px; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Вход в систему</h1>
        <div class="subtitle">Введите свои данные для входа</div>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Логин</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group checkbox">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin: 0;">Запомнить меня</label>
            </div>
            <button type="submit" name="do_login">Войти</button>
        </form>
        <hr>
        <div class="link">
            Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
        </div>
        <div class="test-data">
            <strong>Тестовые данные:</strong><br>
            Зарегистрируйте нового пользователя
        </div>
    </div>
</body>
</html>