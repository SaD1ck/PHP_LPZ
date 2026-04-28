<?php
require_once 'functions.php';

// Получаем текущего пользователя, если он авторизован
$user = isLoggedIn() ? getCurrentUser() : null;

// Определяем тему оформления
$theme = $user ? $user['theme'] : ($_COOKIE['theme'] ?? 'light');

// Если пользователь авторизован, получаем его статистику
if ($user) {
    $notes = getUserNotes($user['id']);
    $stats = getNotesStats($user['id']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный блокнот</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="<?php echo $theme; ?>">
    <div class="container">
        <!-- ШАПКА САЙТА -->
        <div class="header">
            <div class="logo">
                <a href="index.php">📓 Личный блокнот</a>
            </div>
            <div class="nav">
                <?php if ($user): ?>
                    <!-- Меню для авторизованных пользователей -->
                    <a href="index.php">🏠 Главная</a>
                    <a href="notes.php">📝 Мои заметки</a>
                    <a href="settings.php">⚙️ Настройки</a>
                    <a href="logout.php" class="btn-logout">🚪 Выйти</a>
                <?php else: ?>
                    <!-- Меню для гостей -->
                    <a href="login.php">🔑 Вход</a>
                    <a href="register.php">📝 Регистрация</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ОСНОВНОЙ КОНТЕНТ -->
        <div class="content">
            <?php if ($user): ?>
                <!-- СТРАНИЦА ДЛЯ АВТОРИЗОВАННОГО ПОЛЬЗОВАТЕЛЯ -->
                <h1>Добро пожаловать, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                
                <!-- Статистика -->
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Всего заметок</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['created_today']; ?></div>
                        <div class="stat-label">Создано сегодня</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            <?php echo $stats['last_updated'] 
                                ? date('d.m', strtotime($stats['last_updated'])) 
                                : '-'; ?>
                        </div>
                        <div class="stat-label">Последнее обновление</div>
                    </div>
                </div>

                <!-- Быстрое добавление заметки -->
                <div class="quick-form">
                    <h2>📝 Быстрая заметка</h2>
                    <form action="note_add.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="title" placeholder="Заголовок" required>
                        </div>
                        <div class="form-group">
                            <textarea name="content" placeholder="Текст заметки..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">➕ Добавить заметку</button>
                    </form>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="notes.php" class="btn">📋 Перейти ко всем заметкам</a>
                </div>
            <?php else: ?>
                <!-- СТРАНИЦА ДЛЯ ГОСТЕЙ -->
                <h1>Добро пожаловать в Личный блокнот!</h1>
                <p>Ваше личное пространство для заметок и идей.</p>

                <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
                    <a href="register.php" class="btn btn-primary">📝 Зарегистрироваться</a>
                    <a href="login.php" class="btn">🔑 Войти</a>
                </div>

                <!-- Информация о возможностях -->
                <div class="card" style="margin-top: 30px;">
                    <h3>✨ Возможности блокнота</h3>
                    <ul style="margin-left: 20px; line-height: 1.8;">
                        <li>✅ Создание, редактирование и удаление заметок</li>
                        <li>✅ Поиск по заголовкам и тексту</li>
                        <li>✅ Настройка темы оформления</li>
                        <li>✅ Безопасное хранение данных в MySQL</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- ФУТЕР -->
        <div class="footer">
            <p>Личный блокнот &copy; 2025 | Данные хранятся в MySQL</p>
        </div>
    </div>
</body>
</html>