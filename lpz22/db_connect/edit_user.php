<?php
// edit_user.php - страница для редактирования пользователя
require_once 'config.php';

// Получаем ID пользователя из URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    header('Location: users.php');
    exit();
}

$conn = getConnection();

// Получаем данные пользователя для предзаполнения формы
$sql = "SELECT id, username, email, age FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: users.php?error=1');
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $age = $_POST['age'] ?? 0;
    
    // Валидация данных
    if (!empty($username) && !empty($email) && $age > 0) {
        // ЗАДАНИЕ 3: Обновление данных пользователя
        $update_sql = "UPDATE users SET username = ?, email = ?, age = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssii", $username, $email, $age, $user_id);
        
        if ($update_stmt->execute()) {
            $update_stmt->close();
            closeConnection($conn);
            header('Location: user_detail.php?id=' . $user_id . '&updated=1');
            exit();
        } else {
            $error_message = "Ошибка при обновлении данных: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        $error_message = "Все поля должны быть заполнены!";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .edit-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .edit-card h1 {
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #666;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-save {
            flex: 1;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-cancel {
            flex: 1;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-card">
            <h1>✏️ Редактирование пользователя</h1>
            
            <div class="info-box">
                <strong>ID пользователя:</strong> <?php echo htmlspecialchars($user['id']); ?>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_user.php?id=<?php echo $user_id; ?>">
                <div class="form-group">
                    <label for="username">Имя пользователя:</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="<?php echo htmlspecialchars($user['username']); ?>" 
                        required
                        placeholder="Введите имя пользователя"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($user['email']); ?>" 
                        required
                        placeholder="Введите email адрес"
                    >
                </div>

                <div class="form-group">
                    <label for="age">Возраст:</label>
                    <input 
                        type="number" 
                        id="age" 
                        name="age" 
                        value="<?php echo htmlspecialchars($user['age']); ?>" 
                        min="1" 
                        max="150" 
                        required
                        placeholder="Введите возраст"
                    >
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-save">💾 Сохранить изменения</button>
                    <a href="user_detail.php?id=<?php echo $user_id; ?>" class="btn-cancel">❌ Отмена</a>
                </div>
            </form>
        </div>
    </div>

    <?php closeConnection($conn); ?>
</body>
</html>
