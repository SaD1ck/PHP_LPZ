<?php
// user_detail.php - страница с полной информацией о пользователе
require_once 'config.php';

// Получаем ID пользователя из URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    header('Location: users.php');
    exit();
}

$conn = getConnection();

// ЗАДАНИЕ 2: Запрос для получения информации о конкретном пользователе
$sql = "SELECT id, username, email, age, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="container" style="text-align: center;">
            <h2>❌ Пользователь не найден</h2>
            <a href="users.php" class="btn">← Вернуться к списку</a>
          </div>';
    $stmt->close();
    closeConnection($conn);
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о пользователе - <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .user-detail-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .user-detail-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .user-detail-card h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 16px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
            width: 40%;
        }

        .detail-value {
            color: #333;
            word-break: break-all;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            text-align: center;
            width: 100%;
        }

        .btn-back {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 600;
        }

        .btn-back:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-edit-detail {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 600;
            margin-top: 20px;
        }

        .btn-edit-detail:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .action-buttons a {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="user-detail-container">
        <div class="user-detail-card">
            <h1>👤 <?php echo htmlspecialchars($user['username']); ?></h1>
            
            <div class="detail-row">
                <div class="detail-label">ID:</div>
                <div class="detail-value"><?php echo htmlspecialchars($user['id']); ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Имя пользователя:</div>
                <div class="detail-value"><?php echo htmlspecialchars($user['username']); ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div class="detail-value">
                    <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" style="color: #667eea;">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </a>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Возраст:</div>
                <div class="detail-value"><?php echo htmlspecialchars($user['age']); ?> лет</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Дата регистрации:</div>
                <div class="detail-value">
                    <?php 
                        $date = new DateTime($user['created_at']);
                        echo $date->format('d.m.Y H:i:s');
                    ?>
                </div>
            </div>

            <div class="action-buttons">
                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit-detail">✏️ Редактировать</a>
                <a href="users.php" class="btn-back">← Вернуться к списку</a>
            </div>
        </div>
    </div>

    <?php closeConnection($conn); ?>
</body>
</html>
