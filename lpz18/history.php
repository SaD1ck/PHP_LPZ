<?php
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();
$messages = getUserMessages($user['login']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои сообщения</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 15px; padding: 35px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { text-align: center; margin-bottom: 10px; color: #333; }
        .subtitle { text-align: center; color: #666; margin-bottom: 25px; font-size: 14px; }
        .message-item { border: 1px solid #eee; border-radius: 10px; padding: 20px; margin-bottom: 20px; transition: box-shadow 0.3s; }
        .message-item:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .message-header { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: #888; flex-wrap: wrap; gap: 10px; }
        .rating { color: #ffc107; font-size: 20px; letter-spacing: 3px; margin-bottom: 10px; }
        .message-text { line-height: 1.6; margin: 15px 0; }
        .empty { text-align: center; padding: 40px; color: #888; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; margin-top: 20px; }
        .btn:hover { background: #5a67d8; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #667eea; text-decoration: none; }
        .stats { background: #f5f5f5; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; }
        hr { margin: 20px 0; border: none; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Мои сообщения</h1>
        <div class="subtitle">История ваших отзывов</div>

        <div class="stats">
            <strong>Всего сообщений:</strong> <?php echo count($messages); ?>
        </div>

        <?php if (empty($messages)): ?>
            <div class="empty">
                <p>У вас пока нет сообщений</p>
                <a href="feedback.php" class="btn">Оставить первый отзыв</a>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message-item">
                    <div class="message-header">
                        <span><?php echo $msg['timestamp']; ?></span>
                        <span><?php echo htmlspecialchars($msg['email']); ?></span>
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

        <hr>
        <div class="back-link">
            <a href="index.php">← Вернуться на главную</a>
        </div>
    </div>
</body>
</html>