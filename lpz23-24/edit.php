<?php
require_once 'config.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = getProductById($id);

if (!$product) {
    $_SESSION['error_message'] = 'Товар не найден';
    header('Location: index.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $category = trim($_POST['category'] ?? '');

    if (empty($name)) {
        $error = 'Название товара обязательно';
    } elseif ($price <= 0) {
        $error = 'Цена должна быть больше 0';
    } elseif ($quantity < 0) {
        $error = 'Количество не может быть отрицательным';
    } else {
        $result = updateProduct($id, $name, $description, $price, $quantity, $category);
        if ($result) {
            $_SESSION['success_message'] = 'Товар «' . htmlspecialchars($name) . '» успешно обновлен!';
            header('Location: index.php');
            exit();
        } else {
            $error = 'Ошибка при обновлении товара';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать товар</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>✏️ Редактирование товара</h1>
        <div class="subtitle">Измените необходимые поля</div>

        <div class="menu">
            <a href="index.php" class="btn">← Назад к списку</a>
            <a href="view.php?id=<?php echo $id; ?>" class="btn btn-info">👁️ Просмотр</a>
        </div>

        <?php if ($error): ?>
            <div class="error-message">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label>Название товара *</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="form-group">
                    <label>Категория</label>
                    <select name="category">
                        <option value="">Выберите категорию</option>
                        <option value="Электроника" <?php echo $product['category'] == 'Электроника' ? 'selected' : ''; ?>>Электроника</option>
                        <option value="Одежда" <?php echo $product['category'] == 'Одежда' ? 'selected' : ''; ?>>Одежда</option>
                        <option value="Книги" <?php echo $product['category'] == 'Книги' ? 'selected' : ''; ?>>Книги</option>
                        <option value="Другое" <?php echo $product['category'] == 'Другое' ? 'selected' : ''; ?>>Другое</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Цена (руб) *</label>
                    <input type="number" step="0.01" name="price" required value="<?php echo $product['price']; ?>">
                </div>
                <div class="form-group">
                    <label>Количество *</label>
                    <input type="number" name="quantity" required value="<?php echo $product['quantity']; ?>">
                </div>
                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
            </form>
        </div>
    </div>
</body>
</html>