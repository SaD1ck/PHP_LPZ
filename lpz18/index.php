<?php
require_once 'functions.php';

$user = getCurrentUser();
$theme = $user ? $user['theme'] : 'light';

if ($user && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_theme'])) {
    $new_theme = $_POST['theme'] ?? 'light';
    if (in_array($new_theme, ['light', 'dark'])) {
        updateUserTheme($user['login'], $new_theme);
        setcookie('user_theme', $new_theme, time() + (86400 * 365), '/');
        header('Location: index.php');
        exit();
    }
}

$recent_messages = array_slice(getAllMessages(), 0, 5);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: <?php echo $theme === 'dark' ? '#1a1a2e' : '#f0f2f5'; ?>; color: <?php echo $theme === 'dark' ? '#eee' : '#333'; ?>; min-height: 100vh; }
        .header { background: <?php echo $theme === 'dark' ? '#16213e' : 'white'; ?>; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 15px 0; position: sticky; top: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .nav { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .logo { font-size: 24px; font-weight: bold; color: #667eea; }
        .nav-links { display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .nav-links a { color: <?php echo $theme === 'dark' ? '#eee' : '#555'; ?>; text-decoration: none; }
        .nav-links a:hover { color: #667eea; }
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .btn-primary { background: #667eea; color: white; }
        .btn-outline { border: 1px solid #667eea; color: #667eea; }
        .main { padding: 40px 0; }
        .welcome-card, .messages-card, .info-card { background: <?php echo $theme === 'dark' ? '#16213e' : 'white'; ?>; border-radius: 15px; padding: 25px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 20px; color: #667eea; }
        .message-item { border-bottom: 1px solid <?php echo $theme === 'dark' ? '#2a2a3e' : '#eee'; ?>; padding: 15px 0; }
        .message-item:last-child { border-bottom: none; }
        .message-header { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #888; }
        .message-text { margin: 10px 0; line-height: 1.5; }
        .rating { color: #ffc107; font-size: 16px; letter-spacing: 2px; }
        .footer { background: <?php echo $theme === 'dark' ? '#16213e' : '#f8f9fa'; ?>; text-align: center; padding: 20px; margin-top: 40px; font-size: 14px; }
        .theme-form { display: inline; }
        .theme-form select { padding: 5px; border-radius: 5px; border: 1px solid #ddd; }
        .theme-form button { padding: 5px 10px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav">
                <div class="logo">Feedback System</div>
                <div class="nav-links">
                    <a href="index.php">Главная</a>
                    <?php if ($user): ?>
                        <a href="feedback.php">Оставить отзыв</a>
                        <a href="history.php">Мои отзывы</a>
                        <form method="POST" class="theme-form">
                            <select name="theme">
                                <option value="light" <?php echo $theme === 'light' ? 'selected' : ''; ?>>Светлая</option>
                                <option value="dark" <?php echo $theme === 'dark' ? 'selected' : ''; ?>>Тёмная</option>
                            </select>
                            <button type="submit" name="change_theme">Тема</button>
                        </form>
                        <a href="logout.php" class="btn-outline btn">Выйти</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-outline btn">Войти</a>
                        <a href="register.php" class="btn-primary btn">Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="container">
            <?php if ($user): ?>
                <div class="welcome-card">
                    <h2>Добро пожаловать, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                    <p>Вы вошли как <?php echo htmlspecialchars($user['login']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <p>Дата регистрации: <?php echo $user['created_at']; ?></p>
                    <div style="margin-top: 15px;">
                        <a href="feedback.php" class="btn-primary btn">Оставить отзыв</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="welcome-card">
                    <h2>Добро пожаловать!</h2>
                    <p>Это система обратной связи. <a href="login.php">Войдите</a> или <a href="register.php">зарегистрируйтесь</a>.</p>
                </div>
            <?php endif; ?>

            <div class="messages-card">
                <h2>Последние отзывы</h2>
                <?php if (empty($recent_messages)): ?>
                    <p>Пока нет ни одного отзыва.</p>
                <?php else: ?>
                    <?php foreach ($recent_messages as $msg): ?>
                        <div class="message-item">
                            <div class="message-header">
                                <span><?php echo htmlspecialchars($msg['name']); ?></span>
                                <span><?php echo $msg['timestamp']; ?></span>
                            </div>
                            <div class="rating">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $msg['rating'] ? '★' : '☆';
                                }
                                ?>
                            </div>
                            <div class="message-text">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="info-card">
                <h2>О системе</h2>
                <ul style="margin-left: 20px; margin-top: 10px;">
                    <li>Обработка форм (POST/GET)</li>
                    <li>Валидация данных</li>
                    <li>Сессии (авторизация)</li>
                    <li>Cookie (запоминание пользователя)</li>
                    <li>Работа с файловой системой</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <p>Система обратной связи &copy; 2025</p>
        </div>
    </div>
</body>
</html>