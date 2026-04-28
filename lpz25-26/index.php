<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная - Система авторизации</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <a href="index.php">🔐 AuthSystem</a>
            </div>
            <div class="nav">
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php">📊 Личный кабинет</a>
                    <a href="profile.php">👤 Профиль</a>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">👑 Админ-панель</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn-logout">🚪 Выйти</a>
                <?php else: ?>
                    <a href="login.php">🔑 Вход</a>
                    <a href="register.php">📝 Регистрация</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="content">
            <h1>Добро пожаловать в систему авторизации</h1>

            <?php if (isLoggedIn()): ?>
                <!-- Блок для авторизованного пользователя -->
                <div class="alert alert-success">
                    ✅ Вы вошли как <strong><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></strong>
                </div>

                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $_SESSION['role'] ?? 'user'; ?></div>
                        <div class="stat-label">Ваша роль</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <div class="stat-label">Логин</div>
                    </div>
                </div>

                <div class="card">
                    <h3>📋 Что вы можете делать?</h3>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>✅ Просматривать свой профиль</li>
                        <li>✅ Редактировать личную информацию</li>
                        <li>✅ Изменять пароль</li>
                        <?php if (isAdmin()): ?>
                            <li>👑 Управлять пользователями (админ-панель)</li>
                        <?php endif; ?>
                    </ul>
                </div>

            <?php else: ?>
                <!-- Блок для гостя -->
                <div class="alert alert-info">
                    ℹ️ Это демонстрационная система аутентификации и авторизации.
                </div>

                <div class="card">
                    <h3>📝 Возможности системы</h3>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>✅ Регистрация новых пользователей</li>
                        <li>✅ Вход с проверкой пароля</li>
                        <li>✅ Сессии для сохранения состояния входа</li>
                        <li>✅ Защита страниц от неавторизованного доступа</li>
                        <li>✅ Разграничение ролей (пользователь / администратор)</li>
                        <li>✅ Редактирование профиля и смена пароля</li>
                    </ul>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="register.php" class="btn btn-success">📝 Зарегистрироваться</a>
                    <a href="login.php" class="btn">🔑 Войти</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Система аутентификации и авторизации &copy; 2025</p>
        </div>
    </div>
</body>
</html>