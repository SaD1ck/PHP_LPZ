<?php
require_once 'config.php';

// Защита страницы - доступ только для администраторов
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

// Получаем список всех пользователей
$conn   = getConnection();
$result = $conn->query("SELECT id, username, email, full_name, role, is_active, created_at, last_login FROM users ORDER BY id");
$users  = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">🔐 AuthSystem</a></div>
            <div class="nav">
                <a href="dashboard.php">📊 Личный кабинет</a>
                <a href="profile.php">👤 Профиль</a>
                <a href="admin.php" style="background: #667eea; color: white;">👑 Админ-панель</a>
                <a href="logout.php" class="btn-logout">🚪 Выйти</a>
            </div>
        </div>

        <div class="content">
            <h1>👑 Административная панель</h1>

            <div class="alert alert-info">
                ℹ️ Добро пожаловать в админ-панель, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>!
            </div>

            <div class="card">
                <h2>📋 Управление пользователями</h2>
                <p>Всего пользователей: <strong><?php echo count($users); ?></strong></p>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Логин</th>
                            <th>Email</th>
                            <th>Имя</th>
                            <th>Роль</th>
                            <th>Статус</th>
                            <th>Регистрация</th>
                            <th>Последний вход</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                                <td><?php echo $user['role'] == 'admin' ? '👑 Админ' : '👤 Пользователь'; ?></td>
                                <td><?php echo $user['is_active'] ? '✅ Активен' : '❌ Заблокирован'; ?></td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td><?php echo $user['last_login'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Система аутентификации и авторизации &copy; 2025</p>
        </div>
    </div>
</body>
</html>