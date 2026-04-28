<?php
require_once 'config.php';

$products = getAllProducts();
$total_products = count($products);
$total_value = 0;
foreach ($products as $product) {
    $total_value += $product['price'] * $product['quantity'];
}

session_start();
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление товарами</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📦 Управление товарами</h1>
        <div class="subtitle">CRUD-приложение на PHP и MySQL</div>

        <div class="menu">
            <a href="index.php" class="btn btn-active">📋 Список товаров</a>
            <a href="create.php" class="btn btn-primary">➕ Добавить товар</a>
        </div>

        <?php if ($success_message): ?>
            <div class="success-message">✅ <?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message">❌ <?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="stats">
            📊 Всего товаров: <strong><?php echo $total_products; ?></strong> |
            💰 Общая стоимость: <strong><?php echo formatPrice($total_value); ?></strong>
        </div>

        <?php if (empty($products)): ?>
            <div class="error-message">
                📭 Нет товаров. <a href="create.php">Добавьте первый товар</a>
            </div>
        <?php else: ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Название</th><th>Категория</th>
                        <th>Цена</th><th>Количество</th><th>Статус</th><th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php $status = getQuantityStatus($product['quantity']); ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><a href="view.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></td>
                            <td><?php echo htmlspecialchars($product['category'] ?? '-'); ?></td>
                            <td><?php echo formatPrice($product['price']); ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td class="<?php echo $status['class']; ?>"><?php echo $status['text']; ?></td>
                            <td class="actions">
                                <a href="view.php?id=<?php echo $product['id']; ?>" class="btn btn-info">👁️</a>
                                <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">✏️</a>
                                <a href="delete.php?id=<?php echo $product['id']; ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('Удалить товар «<?php echo htmlspecialchars($product['name']); ?>»?')">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="stats" style="margin-top: 20px;">
            <p>💡 <strong>Подсказка:</strong> Нажмите на название товара для просмотра деталей.</p>
        </div>
    </div>
</body>
</html>