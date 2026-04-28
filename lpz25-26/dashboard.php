<?php
require_once 'config.php';

// Защита страницы - доступ только для авторизованных пользователей
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Получаем актуальные данные пользователя из БД
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">🔐 AuthSystem</a></div>
            <div class="nav">
                <a href="dashboard.php" style="background: #667eea; color: white;">📊 Личный кабинет</a>
                <a href="profile.php">👤 Профиль</a>
                <?php if (isAdmin()): ?>
                    <a href="admin.php">👑 Админ-панель</a>
                <?php endif; ?>
                <a href="logout.php" class="btn-logout">🚪 Выйти</a>
            </div>
        </div>

        <div class="content">
            <h1>📊 Личный кабинет</h1>

            <div class="alert alert-success">
                ✅ Добро пожаловать, <strong><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></strong>!
            </div>

            <!-- Краткая статистика по пользователю -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div class="stat-label">Ваш логин</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $_SESSION['role']; ?></div>
                    <div class="stat-label">Ваша роль</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo date('d.m.Y', strtotime($user['created_at'] ?? 'now')); ?></div>
                    <div class="stat-label">Дата регистрации</div>
                </div>
            </div>

            <div class="card">
                <h3>📋 Ваша информация</h3>
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Логин:</td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Email:</td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Полное имя:</td>
                        <td><?php echo htmlspecialchars($user['full_name'] ?? 'Не указано'); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Роль:</td>
                        <td><?php echo $user['role'] == 'admin' ? 'Администратор' : 'Пользователь'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Последний вход:</td>
                        <td><?php echo $user['last_login'] ?? 'Не зафиксирован'; ?></td>
                    </tr>
                </table>
            </div>

            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="profile.php" class="btn btn-success">✏️ Редактировать профиль</a>
                <a href="logout.php" class="btn btn-danger">🚪 Выйти</a>
            </div>
        </div>

        <div class="footer">
            <p>Система аутентификации и авторизации &copy; 2025</p>
        </div>
    </div>
</body>
</html>