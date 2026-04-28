<?php
// users.php - вывод списка пользователей из БД
require_once 'config.php';

$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
$deleted = isset($_GET['deleted']) ? $_GET['deleted'] : null;

$conn = getConnection();

// ЗАДАНИЕ 1: Модификация запросов
// 1. Только пользователей старше 20 лет (age > 20)
// 2. Пользователей, у которых email содержит "mail.ru"
// 3. Сортировка по возрасту (от младших к старшим - ASC)
$sql = "SELECT id, username, email, age, created_at FROM users 
        WHERE age > 20 AND email LIKE '%mail.ru%' 
        ORDER BY age ASC, username ASC";
$result = $conn->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список пользователей</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>👥 Список пользователей</h1>
        
        <?php if ($success == 1): ?>
            <div class="success-message">✅ Пользователь успешно добавлен!</div>
        <?php endif; ?>
        
        <?php if ($deleted == 1): ?>
            <div class="success-message">✅ Пользователь успешно удален!</div>
        <?php endif; ?>
        
        <?php if ($error == 1): ?>
            <div class="error-message">❌ Ошибка при добавлении пользователя</div>
        <?php endif; ?>

        <div class="menu">
            <a href="index.php" class="btn">Главная</a>
            <a href="users.php" class="btn btn-active">Список пользователей</a>
        </div>

        <div class="info">
            <p>Всего пользователей: <strong><?php echo count($users); ?></strong></p>
        </div>

        <?php if (empty($users)): ?>
            <div class="warning">⚠️ В базе данных пока нет пользователей.</div>
        <?php else: ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Возраст</th>
                        <th>Дата регистрации</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['age']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <a href="user_detail.php?id=<?php echo $user['id']; ?>" class="btn-action btn-view">👁️ Смотреть</a>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-action btn-edit">✏️ Редактировать</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?');">🗑️ Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="add-form">
            <h3>➕ Добавить пользователя</h3>
            <form method="POST" action="add_user.php">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Имя пользователя" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="number" name="age" placeholder="Возраст" min="1" max="150" required>
                </div>
                <button type="submit" class="btn-submit">Добавить</button>
            </form>
        </div>

        <div class="code-example">
            <h3>📝 PHP-код для запроса:</h3>
            <pre><code>$sql = "SELECT id, username, email, age, created_at FROM users";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo $row['username'] . " - " . $row['email'];
}</code></pre>
        </div>
    </div>

    <?php closeConnection($conn); ?>
</body>
</html>