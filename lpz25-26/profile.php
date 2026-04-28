<?php
require_once 'config.php';

// Защита страницы — только для авторизованных
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user    = getCurrentUser();
$error   = null;
$success = null;

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_profile'])) {
        // Форма редактирования профиля
        $full_name   = trim($_POST['full_name'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $error_email = validateEmail($email);

        if ($error_email) {
            $error = $error_email;
        } else {
            if (updateProfile($_SESSION['user_id'], $full_name, $email)) {
                $success = 'Профиль успешно обновлен!';
                $user = getCurrentUser(); // Обновляем данные после сохранения
            } else {
                $error = 'Ошибка при обновлении профиля';
            }
        }

    } elseif (isset($_POST['change_password'])) {
        // Форма смены пароля
        $old_password     = $_POST['old_password'] ?? '';
        $new_password     = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($old_password) || empty($new_password)) {
            $error = 'Заполните все поля';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Новый пароль и подтверждение не совпадают';
        } elseif (strlen($new_password) < 6) {
            $error = 'Новый пароль должен быть не менее 6 символов';
        } else {
            $result = changePassword($_SESSION['user_id'], $old_password, $new_password);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">🔐 AuthSystem</a></div>
            <div class="nav">
                <a href="dashboard.php">📊 Личный кабинет</a>
                <a href="profile.php" style="background: #667eea; color: white;">👤 Профиль</a>
                <?php if (isAdmin()): ?>
                    <a href="admin.php">👑 Админ-панель</a>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">🚪 Выйти</a>
            </div>
        </div>

        <div class="content">
            <h1>👤 Профиль пользователя</h1>

            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Форма редактирования профиля -->
            <div class="card">
                <h2>✏️ Редактирование профиля</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Логин (нельзя изменить)</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required
                               value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Полное имя</label>
                        <input type="text" name="full_name"
                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn">💾 Сохранить изменения</button>
                </form>
            </div>

            <!-- Форма смены пароля -->
            <div class="card">
                <h2>🔐 Смена пароля</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Текущий пароль</label>
                        <input type="password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль</label>
                        <input type="password" name="new_password" required>
                        <small>Минимум 6 символов</small>
                    </div>
                    <div class="form-group">
                        <label>Подтверждение нового пароля</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning">🔄 Сменить пароль</button>
                </form>
            </div>
        </div>

        <div class="footer">
            <p>Система аутентификации и авторизации &copy; 2025</p>
        </div>
    </div>
</body>
</html>