<?php
/**
 * functions.php - Все функции приложения "Личный блокнот"
 * 
 * Содержит:
 * - Подключение к БД MySQL
 * - Функции для работы с пользователями (регистрация, вход, выход)
 * - Функции для работы с заметками (CRUD операции)
 * - Функции валидации данных
 */

// ===================================================
// ИНИЦИАЛИЗАЦИЯ И КОНФИГУРАЦИЯ
// ===================================================

// Показываем все ошибки PHP для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Установка кодировки для корректной работы с русским текстом
header('Content-Type: text/html; charset=utf-8');

// Запуск сессии для хранения данных пользователя
session_start();

// ===================================================
// КОНСТАНТЫ ПОДКЛЮЧЕНИЯ К БД
// ===================================================

// Имя хоста БД (указывается при подключении OpenServer)
define('DB_HOST', 'localhost:3307'); // Измените если используется другая версия

// Пользователь БД (по умолчанию root в OpenServer)
define('DB_USER', 'root');

// Пароль БД (по умолчанию пусто в OpenServer)
define('DB_PASS', '');

// Имя базы данных
define('DB_NAME', 'notebook_db');

// ===================================================
// ФУНКЦИЯ ПОДКЛЮЧЕНИЯ К БД
// ===================================================

/**
 * Получает подключение к базе данных
 * 
 * @return mysqli Объект подключения к БД
 * @throws Exception Если подключение не удалось
 */
function getConnection() {
    // Создаем новое подключение к MySQL
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Проверяем, удалось ли подключиться
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }
    
    // Устанавливаем кодировку utf8mb4 для корректной работы с Unicode
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// ===================================================
// ФУНКЦИИ ДЛЯ РАБОТЫ С ПОЛЬЗОВАТЕЛЯМИ
// ===================================================

/**
 * Получить пользователя по логину
 * 
 * @param string $login Логин пользователя
 * @return array|null Массив данных пользователя или null
 */
function getUserByLogin($login) {
    // Получаем подключение к БД
    $conn = getConnection();
    
    // Подготавливаем SQL запрос с параметром
    // ? - это плейсхолдер для защиты от SQL-инъекций
    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
    
    // Привязываем значение к параметру
    // "s" означает string (строка)
    $stmt->bind_param("s", $login);
    
    // Выполняем запрос
    $stmt->execute();
    
    // Получаем результат
    $result = $stmt->get_result();
    
    // Извлекаем один ряд как ассоциативный массив
    $user = $result->fetch_assoc();
    
    // Закрываем prepared statement
    $stmt->close();
    
    // Закрываем подключение
    closeConnection($conn);
    
    return $user;
}

/**
 * Получить пользователя по ID
 * 
 * @param int $id ID пользователя
 * @return array|null Массив данных пользователя или null
 */
function getUserById($id) {
    $conn = getConnection();
    
    // "i" означает integer (целое число)
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    closeConnection($conn);
    
    return $user;
}

/**
 * Регистрация нового пользователя
 * 
 * @param string $login Логин
 * @param string $password Пароль (будет захеширован)
 * @param string $name Имя
 * @param string $email Email
 * @return array Результат с ключами 'success' и 'message'
 */
function registerUser($login, $password, $name, $email) {
    $conn = getConnection();
    
    // Проверяем, существует ли уже пользователь с таким логином или email
    $check = $conn->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
    $check->bind_param("ss", $login, $email);
    $check->execute();
    $result = $check->get_result();
    
    // Если нашли совпадение - пользователь уже существует
    if ($result->num_rows > 0) {
        $check->close();
        closeConnection($conn);
        return [
            'success' => false, 
            'message' => 'Пользователь с таким логином или email уже существует'
        ];
    }
    
    $check->close();
    
    // Хешируем пароль для безопасного хранения
    // password_hash использует алгоритм bcrypt
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Вставляем нового пользователя в таблицу
    $stmt = $conn->prepare(
        "INSERT INTO users (login, password, name, email) VALUES (?, ?, ?, ?)"
    );
    
    // "ssss" - четыре строковых параметра
    $stmt->bind_param("ssss", $login, $password_hash, $name, $email);
    
    // Пытаемся выполнить вставку
    if ($stmt->execute()) {
        $stmt->close();
        closeConnection($conn);
        return [
            'success' => true, 
            'message' => 'Регистрация успешна!'
        ];
    } else {
        $stmt->close();
        closeConnection($conn);
        return [
            'success' => false, 
            'message' => 'Ошибка регистрации'
        ];
    }
}

/**
 * Вход пользователя в систему
 * 
 * @param string $login Логин
 * @param string $password Пароль
 * @param bool $remember Запомнить пользователя (cookie на 30 дней)
 * @return array Результат входа
 */
function loginUser($login, $password, $remember = false) {
    // Ищем пользователя по логину
    $user = getUserByLogin($login);
    
    // Проверяем, существует ли пользователь и верен ли пароль
    // password_verify() безопасно сравнивает пароль с хешем
    if (!$user || !password_verify($password, $user['password'])) {
        return [
            'success' => false, 
            'message' => 'Неверный логин или пароль'
        ];
    }
    
    // Сохраняем данные пользователя в сессию
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_login'] = $user['login'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_theme'] = $user['theme'];
    $_SESSION['logged_in'] = true;
    
    // Если выбрана опция "Запомнить меня"
    if ($remember) {
        // Генерируем случайный токен
        $token = bin2hex(random_bytes(32));
        
        // Сохраняем токен в cookie на 30 дней
        setcookie('remember_token', $token, time() + (86400 * 30), '/');
        setcookie('remember_login', $login, time() + (86400 * 30), '/');
    }
    
    return ['success' => true];
}

/**
 * Выход пользователя из системы
 */
function logoutUser() {
    // Очищаем все данные сессии
    $_SESSION = [];
    
    // Удаляем сессию
    session_destroy();
    
    // Удаляем cookies с "Запомнить меня"
    setcookie('remember_token', '', time() - 3600, '/');
    setcookie('remember_login', '', time() - 3600, '/');
}

/**
 * Проверить, авторизован ли пользователь
 * 
 * @return bool true если авторизован, false если нет
 */
function isLoggedIn() {
    // Проверяем, установлена ли сессия пользователя
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return true;
    }
    
    // Если сессии нет, проверяем cookies "Запомнить меня"
    if (isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_login'])) {
        // Ищем пользователя по логину из cookie
        $user = getUserByLogin($_COOKIE['remember_login']);
        
        if ($user) {
            // Восстанавливаем сессию из данных БД
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_theme'] = $user['theme'];
            $_SESSION['logged_in'] = true;
            return true;
        }
    }
    
    return false;
}

/**
 * Получить текущего авторизованного пользователя
 * 
 * @return array|null Массив данных пользователя или null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return getUserById($_SESSION['user_id']);
}

/**
 * Обновить профиль пользователя (имя и email)
 * 
 * @param int $user_id ID пользователя
 * @param string $name Новое имя
 * @param string $email Новый email
 * @return bool true если успешно, false если ошибка
 */
function updateUser($user_id, $name, $email) {
    $conn = getConnection();
    
    // UPDATE запрос для изменения данных
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    closeConnection($conn);
    
    // Обновляем данные в текущей сессии
    if ($result) {
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
    }
    
    return $result;
}

/**
 * Обновить тему оформления пользователя
 * 
 * @param int $user_id ID пользователя
 * @param string $theme Новая тема (light, dark, blue)
 * @return bool true если успешно
 */
function updateUserTheme($user_id, $theme) {
    $conn = getConnection();
    
    $stmt = $conn->prepare("UPDATE users SET theme = ? WHERE id = ?");
    $stmt->bind_param("si", $theme, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    closeConnection($conn);
    
    // Обновляем данные в сессии
    if ($result) {
        $_SESSION['user_theme'] = $theme;
    }
    
    return $result;
}

/**
 * Изменить пароль пользователя
 * 
 * @param int $user_id ID пользователя
 * @param string $old_password Старый пароль
 * @param string $new_password Новый пароль
 * @return array Результат операции
 */
function changePassword($user_id, $old_password, $new_password) {
    // Получаем текущие данные пользователя
    $user = getUserById($user_id);
    
    // Проверяем, верен ли старый пароль
    if (!password_verify($old_password, $user['password'])) {
        return [
            'success' => false, 
            'message' => 'Неверный текущий пароль'
        ];
    }
    
    // Хешируем новый пароль
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Обновляем пароль в БД
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_hash, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    closeConnection($conn);
    
    if ($result) {
        return [
            'success' => true, 
            'message' => 'Пароль успешно изменен'
        ];
    } else {
        return [
            'success' => false, 
            'message' => 'Ошибка при смене пароля'
        ];
    }
}

// ===================================================
// ФУНКЦИИ ДЛЯ РАБОТЫ С ЗАМЕТКАМИ (CRUD)
// ===================================================

/**
 * Получить все заметки пользователя
 * 
 * @param int $user_id ID пользователя
 * @return array Массив всех заметок
 */
function getUserNotes($user_id) {
    $conn = getConnection();
    
    // SELECT с сортировкой по дате обновления (новые сверху)
    $stmt = $conn->prepare(
        "SELECT * FROM notes WHERE user_id = ? ORDER BY updated_at DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Извлекаем все строки в массив
    $notes = [];
    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
    }
    
    $stmt->close();
    closeConnection($conn);
    
    return $notes;
}

/**
 * Получить одну заметку по ID (с проверкой прав владельца)
 * 
 * @param int $note_id ID заметки
 * @param int $user_id ID пользователя
 * @return array|null Данные заметки или null если не найдена
 */
function getNoteById($note_id, $user_id) {
    $conn = getConnection();
    
    // Проверяем, что заметка принадлежит пользователю
    $stmt = $conn->prepare(
        "SELECT * FROM notes WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $note = $result->fetch_assoc();
    $stmt->close();
    closeConnection($conn);
    
    return $note;
}

/**
 * Добавить новую заметку
 * 
 * @param int $user_id ID владельца
 * @param string $title Заголовок
 * @param string $content Текст
 * @return int|false ID новой заметки или false
 */
function addNote($user_id, $title, $content) {
    $conn = getConnection();
    
    // INSERT с тремя параметрами
    $stmt = $conn->prepare(
        "INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iss", $user_id, $title, $content);
    $result = $stmt->execute();
    
    // Получаем ID вставленной записи
    $new_id = $stmt->insert_id;
    $stmt->close();
    closeConnection($conn);
    
    return $result ? $new_id : false;
}

/**
 * Обновить заметку
 * 
 * @param int $note_id ID заметки
 * @param int $user_id ID пользователя (для проверки прав)
 * @param string $title Новый заголовок
 * @param string $content Новый текст
 * @return bool true если успешно
 */
function updateNote($note_id, $user_id, $title, $content) {
    $conn = getConnection();
    
    // UPDATE с автоматическим обновлением времени изменения
    $stmt = $conn->prepare(
        "UPDATE notes SET title = ?, content = ?, updated_at = NOW() 
         WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param("ssii", $title, $content, $note_id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    closeConnection($conn);
    
    return $result;
}

/**
 * Удалить заметку
 * 
 * @param int $note_id ID заметки
 * @param int $user_id ID пользователя (для проверки прав)
 * @return bool true если успешно
 */
function deleteNote($note_id, $user_id) {
    $conn = getConnection();
    
    // DELETE с проверкой прав
    $stmt = $conn->prepare(
        "DELETE FROM notes WHERE id = ? AND user_id = ?"
    );
    $stmt->bind_param("ii", $note_id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    closeConnection($conn);
    
    return $result;
}

/**
 * Поиск заметок по заголовку и содержанию
 * 
 * @param int $user_id ID пользователя
 * @param string $query Поисковая строка
 * @return array Массив найденных заметок
 */
function searchNotes($user_id, $query) {
    $conn = getConnection();
    
    // Подготавливаем строку поиска (добавляем % для LIKE)
    $search = "%{$query}%";
    
    // Ищем в заголовке ИЛИ в содержимом
    $stmt = $conn->prepare(
        "SELECT * FROM notes WHERE user_id = ? 
         AND (title LIKE ? OR content LIKE ?) 
         ORDER BY updated_at DESC"
    );
    $stmt->bind_param("iss", $user_id, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notes = [];
    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
    }
    
    $stmt->close();
    closeConnection($conn);
    
    return $notes;
}

/**
 * Получить статистику по заметкам пользователя
 * 
 * @param int $user_id ID пользователя
 * @return array Статистика [total, created_today, last_updated]
 */
function getNotesStats($user_id) {
    // Получаем все заметки
    $notes = getUserNotes($user_id);
    $total = count($notes);
    
    // Считаем заметки, созданные сегодня
    $today = date('Y-m-d');
    $created_today = 0;
    
    foreach ($notes as $note) {
        // Сравниваем дату создания с сегодняшней
        if (strpos($note['created_at'], $today) === 0) {
            $created_today++;
        }
    }
    
    // Последнее обновление (из первой заметки, так как сортировка DESC)
    $last_updated = !empty($notes) ? $notes[0]['updated_at'] : null;
    
    return [
        'total' => $total,
        'created_today' => $created_today,
        'last_updated' => $last_updated
    ];
}

// ===================================================
// ФУНКЦИИ ВАЛИДАЦИИ
// ===================================================

/**
 * Валидировать логин
 * 
 * @param string $login Логин
 * @return string|null Сообщение об ошибке или null
 */
function validateLogin($login) {
    if (empty($login)) {
        return 'Логин обязателен';
    }
    if (strlen($login) < 3) {
        return 'Логин должен быть не менее 3 символов';
    }
    if (strlen($login) > 50) {
        return 'Логин не должен превышать 50 символов';
    }
    // Проверяем, что логин содержит только буквы, цифры и подчеркивание
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
        return 'Логин может содержать только буквы, цифры и подчеркивание';
    }
    return null;
}

/**
 * Валидировать пароль
 * 
 * @param string $password Пароль
 * @return string|null Сообщение об ошибке или null
 */
function validatePassword($password) {
    if (empty($password)) {
        return 'Пароль обязателен';
    }
    if (strlen($password) < 6) {
        return 'Пароль должен быть не менее 6 символов';
    }
    return null;
}

/**
 * Валидировать имя
 * 
 * @param string $name Имя
 * @return string|null Сообщение об ошибке или null
 */
function validateName($name) {
    if (empty($name)) {
        return 'Имя обязательно';
    }
    if (strlen($name) < 2) {
        return 'Имя должно быть не менее 2 символов';
    }
    if (strlen($name) > 100) {
        return 'Имя не должно превышать 100 символов';
    }
    // Проверяем, что имя содержит только буквы, пробелы и дефисы
    if (!preg_match('/^[a-zA-Zа-яА-Я\s-]+$/u', $name)) {
        return 'Имя может содержать только буквы, пробелы и дефисы';
    }
    return null;
}

/**
 * Валидировать email
 * 
 * @param string $email Email
 * @return string|null Сообщение об ошибке или null
 */
function validateEmail($email) {
    if (empty($email)) {
        return 'Email обязателен';
    }
    // Встроенная функция PHP для проверки email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Введите корректный email';
    }
    return null;
}

/**
 * Валидировать заголовок заметки
 * 
 * @param string $title Заголовок
 * @return string|null Сообщение об ошибке или null
 */
function validateNoteTitle($title) {
    if (empty($title)) {
        return 'Заголовок обязателен';
    }
    if (strlen($title) < 3) {
        return 'Заголовок должен быть не менее 3 символов';
    }
    if (strlen($title) > 100) {
        return 'Заголовок не должен превышать 100 символов';
    }
    return null;
}

/**
 * Закрыть подключение к БД
 * 
 * @param mysqli $conn Объект подключения
 */
function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

?>