<?php
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $rating = $_POST['rating'] ?? '';

    $error_name = validateName($name);
    $error_email = validateEmail($email);
    $error_message = validateMessage($message);

    if ($error_name) {
        $error = $error_name;
    } elseif ($error_email) {
        $error = $error_email;
    } elseif ($error_message) {
        $error = $error_message;
    } elseif (empty($rating) || !in_array($rating, ['1','2','3','4','5'])) {
        $error = 'Выберите оценку';
    } else {
        saveMessage($user['login'], $name, $email, $message, $rating);
        $success = 'Спасибо за ваш отзыв! Он был сохранен.';
        $_POST = [];
    }
}

$default_name = $_POST['name'] ?? $user['name'];
$default_email = $_POST['email'] ?? $user['email'];
$default_message = $_POST['message'] ?? '';
$default_rating = $_POST['rating'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обратная связь</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 35px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { text-align: center; margin-bottom: 10px; color: #333; }
        .subtitle { text-align: center; color: #666; margin-bottom: 25px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; font-family: inherit; }
        textarea { resize: vertical; }
        input:focus, select:focus, textarea:focus { border-color: #667eea; outline: none; }
        .rating-group { display: flex; gap: 15px; flex-wrap: wrap; }
        .rating-option { display: flex; align-items: center; gap: 5px; }
        .rating-option input { width: auto; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; font-weight: bold; }
        button:hover { opacity: 0.9; transform: translateY(-2px); }
        .error { background: #fee; color: #c33; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #c33; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4CAF50; }
        .hint { font-size: 12px; color: #888; margin-top: 5px; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #667eea; text-decoration: none; }
        hr { margin: 20px 0; border: none; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Обратная связь</h1>
        <div class="subtitle">Мы ценим ваше мнение!</div>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Ваше имя *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($default_name); ?>" required>
                <div class="hint">Минимум 2 символа (только буквы)</div>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($default_email); ?>" required>
            </div>
            <div class="form-group">
                <label>Оценка сайта *</label>
                <div class="rating-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="rating-option">
                            <input type="radio" name="rating" value="<?php echo $i; ?>" <?php echo $default_rating == $i ? 'checked' : ''; ?>>
                            <?php echo str_repeat('★', $i) . str_repeat('☆', 5 - $i); ?>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="form-group">
                <label>Ваше сообщение *</label>
                <textarea name="message" rows="6" required placeholder="Напишите ваше сообщение..."><?php echo htmlspecialchars($default_message); ?></textarea>
                <div class="hint">Минимум 10 символов</div>
            </div>
            <button type="submit" name="submit_feedback">Отправить отзыв</button>
        </form>
        <hr>
        <div class="back-link">
            <a href="index.php">← Вернуться на главную</a>
        </div>
    </div>
</body>
</html>