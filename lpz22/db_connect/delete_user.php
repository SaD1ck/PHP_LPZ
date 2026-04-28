<?php
// delete_user.php - удаление пользователя
require_once 'config.php';

// Получаем ID пользователя из URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id <= 0) {
    header('Location: users.php');
    exit();
}

$conn = getConnection();

// Проверяем существование пользователя перед удалением
$check_sql = "SELECT id FROM users WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    $check_stmt->close();
    closeConnection($conn);
    header('Location: users.php?error=1');
    exit();
}

$check_stmt->close();

// ЗАДАНИЕ 3: Удаление пользователя из базы данных
$delete_sql = "DELETE FROM users WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $user_id);

if ($delete_stmt->execute()) {
    $delete_stmt->close();
    closeConnection($conn);
    // Перенаправляем на страницу списка с параметром успешного удаления
    header('Location: users.php?deleted=1');
    exit();
} else {
    $delete_stmt->close();
    closeConnection($conn);
    header('Location: users.php?error=1');
    exit();
}
?>
