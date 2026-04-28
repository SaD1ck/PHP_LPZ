<?php
require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($id);

if (!$product) {
    session_start();
    $_SESSION['error_message'] = 'Товар не найден';
    header('Location: index.php');
    exit();
}

$status = getQuantityStatus($product['quantity']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📦 Детальная информация</h1>

        <div class="menu">
            <a href="index.php" class="btn">← Назад к списку</a>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">✏️ Редактировать</a>
            <a href="delete.php?id=<?php echo $id; ?>"
               class="btn btn-danger"
               onclick="return confirm('Удалить товар?')">🗑️ Удалить</a>
        </div>

        <div class="product-card">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <?php if (!empty($product['category'])): ?>
                <div class="meta">📂 Категория: <?php echo htmlspecialchars($product['category']); ?></div>
            <?php endif; ?>
            <div class="price"><?php echo formatPrice($product['price']); ?></div>
            <div class="meta">
                📦 Количество: <?php echo $product['quantity']; ?> шт.<br>
                📊 Статус: <span class="<?php echo $status['class']; ?>"><?php echo $status['text']; ?></span>
            </div>
            <?php if (!empty($product['description'])): ?>
                <div class="meta">
                    <strong>📝 Описание:</strong><br>
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
            <?php endif; ?>
            <div class="meta" style="font-size: 12px; color: #888; margin-top: 20px;">
                🕐 Создан: <?php echo $product['created_at']; ?><br>
                🕐 Обновлен: <?php echo $product['updated_at']; ?>
            </div>
        </div>

        <div class="stats">
            <p>💡 <strong>Совет:</strong> Используйте кнопки выше для редактирования или удаления товара.</p>
        </div>
    </div>
</body>
</html>