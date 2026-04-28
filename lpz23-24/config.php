<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define('DB_HOST', 'localhost:3307'); // Ваша версия MySQL!
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'crud_app');

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

function getAllProducts() {
    $conn = getConnection();
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $result = $conn->query($sql);
    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    closeConnection($conn);
    return $products;
}

function getProductById($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = null;
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
    closeConnection($conn);
    return $product;
}

function createProduct($name, $description, $price, $quantity, $category) {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $description, $price, $quantity, $category);
    $result = $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    return $result ? $new_id : false;
}

function updateProduct($id, $name, $description, $price, $quantity, $category) {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, category = ? WHERE id = ?");
    $stmt->bind_param("ssdisi", $name, $description, $price, $quantity, $category, $id);
    $result = $stmt->execute();
    $stmt->close();
    closeConnection($conn);
    return $result;
}

function deleteProduct($id) {
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    closeConnection($conn);
    return $result;
}

function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' руб.';
}

function getQuantityStatus($quantity) {
    if ($quantity <= 0) {
        return ['class' => 'out-of-stock', 'text' => 'Нет в наличии'];
    } elseif ($quantity < 5) {
        return ['class' => 'low-stock', 'text' => 'Осталось мало: ' . $quantity];
    } else {
        return ['class' => 'in-stock', 'text' => 'В наличии: ' . $quantity];
    }
}
?>