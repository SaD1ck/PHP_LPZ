<?php
require_once 'functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = null;
$success = null;
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $form_data = ['login' => $login, 'name' => $name, 'email' => $email];

    $error_login = validateLogin($login);
    $error_password = validatePassword($password);
    $error_name = validateName($name);
    $error_email = validateEmail($email);

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
    } elseif (findUserByLogin($login)) {
        $error = 'Пользователь с таким логином уже существует';
    } else {
        $user = [
            'login' => $login,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'name' => $name,
            'email' => $email,
            'theme' => 'light',
            'created_at' => date('Y-m-d H:i:s')
        ];
        saveUser($user);
        $success = 'Регистрация прошла успешно! Теперь вы можете войти.';
        $form_data = [];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { max-width: 450px; width: 100%; background: white; border-radius: 15px; padding: 35px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { text-align: center; margin-bottom: 10px; color: #333; }
        .subtitle { text-align: center; color: #666; margin-bottom: 25px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; }
        input:focus { border-color: #667eea; outline: none; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: bold; }
        button:hover { opacity: 0.9; transform: translateY(-2px); }
        .error { background: #fee; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #c33; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4CAF50; }
        .hint { font-size: 12px; color: #888; margin-top: 5px; }
        .link { text-align: center; margin-top: 20px; }
        .link a { color: #667eea; text-decoration: none; }
        hr { margin: 20px 0; border: none; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Регистрация</h1>
        <div class="subtitle">Создайте аккаунт для доступа к системе</div>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Логин *</label>
                <input type="text" name="login" value="<?php echo htmlspecialchars($form_data['login'] ?? ''); ?>" required>
                <div class="hint">Только буквы, цифры и _ (минимум 3 символа)</div>
            </div>
            <div class="form-group">
                <label>Пароль *</label>
                <input type="password" name="password" required>
                <div class="hint">Минимум 6 символов</div>
            </div>
            <div class="form-group">
                <label>Подтверждение пароля *</label>
                <input type="password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label>Ваше имя *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required>
                <div class="hint">Минимум 2 символа (только буквы)</div>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                <div class="hint">example@mail.ru</div>
            </div>
            <button type="submit" name="register">Зарегистрироваться</button>
        </form>
        <hr>
        <div class="link">
            Уже есть аккаунт? <a href="login.php">Войти</a>
        </div>
    </div>
</body>
</html>