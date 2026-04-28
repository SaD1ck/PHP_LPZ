<?php
// index.php - главная страница
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Работа с базой данных</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📚 Работа с базой данных MySQL из PHP</h1>
        
        <div class="menu">
            <a href="index.php" class="btn btn-active">Главная</a>
            <a href="users.php" class="btn">Список пользователей</a>
        </div>

        <div class="info">
            <h2>📊 Информация о подключении</h2>
            <table class="info-table">
                <tr>
                    <td>Хост (DB_HOST):</td>
                    <td><code><?php echo DB_HOST; ?></code></td>
                </tr>
                <tr>
                    <td>Имя пользователя:</td>
                    <td><code><?php echo DB_USER; ?></code></td>
                </tr>
                <tr>
                    <td>Имя базы данных:</td>
                    <td><code><?php echo DB_NAME; ?></code></td>
                </tr>
                <tr>
                    <td>Статус подключения:</td>
                    <td class="success">✅ Подключено</td>
                </tr>
            </table>
        </div>

        <div class="info">
            <h2>📋 Что мы научились делать:</h2>
            <ul>
                <li>✅ Подключаться к базе данных MySQL из PHP</li>
                <li>✅ Выполнять SQL-запросы через PHP</li>
                <li>✅ Выводить данные из таблицы users</li>
                <li>✅ Добавлять новых пользователей</li>
                <li>✅ Редактировать и удалять записи</li>
            </ul>
        </div>

        <div class="demo">
            <h2>🎯 Демонстрация работы</h2>
            <p>Перейдите на страницу <a href="users.php">«Список пользователей»</a>, 
            чтобы увидеть данные из базы данных.</p>
        </div>

        <?php
        // Проверяем подключение к БД
        $conn = getConnection();
        echo '<div class="success">✅ Подключение к базе данных успешно!</div>';
        closeConnection($conn);
        ?>
    </div>
</body>
</html>