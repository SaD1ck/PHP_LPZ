<?php
require_once 'functions.php';

// Проверяем авторизацию
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();
$theme = $user['theme'];

// Получаем все заметки пользователя
$notes = getUserNotes($user['id']);

// Обработка поиска
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $notes = searchNotes($user['id'], $search);
}

$stats = getNotesStats($user['id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заметки</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="<?php echo $theme; ?>">
    <div class="container">
        <div class="header">
            <div class="logo"><a href="index.php">📓 Личный блокнот</a></div>
            <div class="nav">
                <a href="index.php">🏠 Главная</a>
                <a href="notes.php" style="background: #667eea; color: white;">📝 Мои заметки</a>
                <a href="settings.php">⚙️ Настройки</a>
                <a href="logout.php" class="btn-logout">🚪 Выйти</a>
            </div>
        </div>

        <div class="content">
            <h1>📝 Мои заметки</h1>

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
            </div>

            <!-- Поиск -->
            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 10px; width: 100%;">
                    <input type="text" name="search" placeholder="🔍 Поиск по заметкам..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn">Найти</button>
                    <?php if ($search): ?>
                        <a href="notes.php" class="btn btn-warning">Сбросить</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Кнопка добавления -->
            <div style="margin-bottom: 20px;">
                <a href="note_add.php" class="btn btn-primary">➕ Новая заметка</a>
            </div>

            <?php if (empty($notes)): ?>
                <!-- Если заметок нет -->
                <div class="alert alert-info">
                    <?php if ($search): ?>
                        🔍 По запросу "<?php echo htmlspecialchars($search); ?>" ничего не найдено.
                    <?php else: ?>
                        📭 У вас пока нет заметок. <a href="note_add.php">Создайте первую заметку</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Таблица заметок -->
                <table class="notes-table">
                    <thead>
                        <tr>
                            <th>Заголовок</th>
                            <th>Дата создания</th>
                            <th>Дата изменения</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notes as $note): ?>
                            <tr>
                                <td>
                                    <a href="note_view.php?id=<?php echo $note['id']; ?>">
                                        <?php echo htmlspecialchars($note['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($note['created_at'])); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($note['updated_at'])); ?></td>
                                <td class="actions">
                                    <a href="note_view.php?id=<?php echo $note['id']; ?>" 
                                       class="btn btn-info btn-sm">👁</a>
                                    <a href="note_edit.php?id=<?php echo $note['id']; ?>" 
                                       class="btn btn-warning btn-sm">✏️</a>
                                    <a href="note_delete.php?id=<?php echo $note['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Удалить заметку?')">🗑</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Личный блокнот &copy; 2025</p>
        </div>
    </div>
</body>
</html>