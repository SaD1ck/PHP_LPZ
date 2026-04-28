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

$result = deleteProduct($id);

if ($result) {
    $_SESSION['success_message'] = 'Товар «' . htmlspecialchars($product['name']) . '» успешно удален!';
} else {
    $_SESSION['error_message'] = 'Ошибка при удалении товара';
}

header('Location: index.php');
exit();
?>