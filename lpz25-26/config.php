<?php
// config.php - настройки подключения и глобальные функции

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

session_start();

// Параметры подключения
define('DB_HOST', 'localhost:3307');  // Ваша версия MySQL!
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'auth_app');

// Функция подключения к БД
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

// Функция закрытия подключения
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// ============================================
// ФУНКЦИИ АУТЕНТИФИКАЦИИ
// ============================================

// Регистрация нового пользователя
function registerUser($username, $email, $password, $full_name = '') {
    $conn = getConnection();

    // Проверяем, существует ли пользователь
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        closeConnection($conn);
        return ['success' => false, 'message' => 'Пользователь с таким логином или email уже существует'];
    }

    // Хешируем пароль
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Добавляем пользователя
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password_hash, $full_name);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $stmt->close();
        closeConnection($conn);
        return ['success' => true, 'user_id' => $user_id, 'message' => 'Регистрация успешна!'];
    } else {
        $stmt->close();
        closeConnection($conn);
        return ['success' => false, 'message' => 'Ошибка регистрации'];
    }
}

// Вход пользователя
function loginUser($username, $password, $remember = false) {
    $conn = getConnection();

    // Ищем пользователя (только активных)
    $stmt = $conn->prepare("SELECT id, username, email, password_hash, full_name, role FROM users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        closeConnection($conn);
        return ['success' => false, 'message' => 'Неверный логин или пароль'];
    }

    $user = $result->fetch_assoc();

    // Проверяем пароль
    if (password_verify($password, $user['password_hash'])) {
        // Обновляем время последнего входа
        $update = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->bind_param("i", $user['id']);
        $update->execute();

        // Сохраняем данные в сессию
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['logged_in'] = true;

        // Запоминаем пользователя (Cookie на 30 дней)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (86400 * 30), '/');
            // В реальном проекте токен нужно сохранить в БД
        }

        closeConnection($conn);
        return ['success' => true, 'user' => $user];
    } else {
        closeConnection($conn);
        return ['success' => false, 'message' => 'Неверный логин или пароль'];
    }
}

// Проверка, авторизован ли пользователь
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Проверка, является ли пользователь админом
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Получение данных текущего пользователя из БД
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    $conn = getConnection();
    $stmt = $conn->prepare("SELECT id, username, email, full_name, role, created_at, last_login FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->fetch_assoc();
    closeConnection($conn);

    return $user;
}

// Выход из системы
function logoutUser() {
    // Очищаем сессию
    $_SESSION = array();

    // Удаляем cookie сессии
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Удаляем cookie запоминания
    setcookie('remember_token', '', time() - 3600, '/');

    // Уничтожаем сессию
    session_destroy();
}

// Обновление профиля пользователя
function updateProfile($user_id, $full_name, $email) {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $full_name, $email, $user_id);

    $result = $stmt->execute();

    // Обновляем данные в сессии
    if ($result) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email']     = $email;
    }

    $stmt->close();
    closeConnection($conn);

    return $result;
}

// Смена пароля
function changePassword($user_id, $old_password, $new_password) {
    $conn = getConnection();

    // Получаем текущий хеш пароля
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Проверяем старый пароль
    if (!password_verify($old_password, $user['password_hash'])) {
        closeConnection($conn);
        return ['success' => false, 'message' => 'Неверный текущий пароль'];
    }

    // Устанавливаем новый пароль
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $update->bind_param("si", $new_hash, $user_id);

    $result = $update->execute();

    $update->close();
    closeConnection($conn);

    if ($result) {
        return ['success' => true, 'message' => 'Пароль успешно изменен'];
    } else {
        return ['success' => false, 'message' => 'Ошибка при смене пароля'];
    }
}

// Валидация данных — проверка логина
function validateUsername($username) {
    if (empty($username)) {
        return 'Логин обязателен';
    }
    if (strlen($username) < 3) {
        return 'Логин должен быть не менее 3 символов';
    }
    if (strlen($username) > 50) {
        return 'Логин не должен превышать 50 символов';
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return 'Логин может содержать только буквы, цифры и подчеркивание';
    }
    return null; // null = ошибок нет
}

// Валидация данных — проверка email
function validateEmail($email) {
    if (empty($email)) {
        return 'Email обязателен';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Введите корректный email';
    }
    return null;
}

// Валидация данных — проверка пароля
function validatePassword($password) {
    if (empty($password)) {
        return 'Пароль обязателен';
    }
    if (strlen($password) < 6) {
        return 'Пароль должен быть не менее 6 символов';
    }
    return null;
}

// Валидация данных — проверка полного имени
function validateFullName($name) {
    if (!empty($name) && strlen($name) > 100) {
        return 'Имя не должно превышать 100 символов';
    }
    return null;
}
?>