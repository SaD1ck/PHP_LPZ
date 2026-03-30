<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
session_start();

define('USERS_FILE', __DIR__ . '/data/users.txt');
define('MESSAGES_FILE', __DIR__ . '/data/messages.txt');

function getAllUsers() {
    $users = [];
    if (!file_exists(USERS_FILE)) {
        return $users;
    }
    $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (empty(trim($line))) continue;
        $parts = explode('|', $line);
        $users[] = [
            'login' => $parts[0],
            'password' => $parts[1],
            'name' => $parts[2],
            'email' => $parts[3],
            'theme' => $parts[4] ?? 'light',
            'created_at' => $parts[5] ?? ''
        ];
    }
    return $users;
}

function findUserByLogin($login) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['login'] === $login) {
            return $user;
        }
    }
    return null;
}

function saveUser($user) {
    $line = implode('|', [
        $user['login'],
        $user['password'],
        $user['name'],
        $user['email'],
        $user['theme'],
        $user['created_at']
    ]);
    file_put_contents(USERS_FILE, $line . PHP_EOL, FILE_APPEND);
}

function updateUserTheme($login, $theme) {
    $users = getAllUsers();
    $new_users = [];
    foreach ($users as $user) {
        if ($user['login'] === $login) {
            $user['theme'] = $theme;
        }
        $new_users[] = implode('|', $user);
    }
    file_put_contents(USERS_FILE, implode(PHP_EOL, $new_users) . PHP_EOL);
}

function getAllMessages() {
    $messages = [];
    if (!file_exists(MESSAGES_FILE)) {
        return $messages;
    }
    $lines = file(MESSAGES_FILE, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        if (empty(trim($line))) continue;
        $parts = explode('|', $line);
        $messages[] = [
            'user_login' => $parts[0],
            'timestamp' => $parts[1],
            'name' => $parts[2],
            'email' => $parts[3],
            'message' => $parts[4],
            'rating' => $parts[5]
        ];
    }
    return array_reverse($messages);
}

function getUserMessages($login) {
    $all = getAllMessages();
    $user_messages = [];
    foreach ($all as $msg) {
        if ($msg['user_login'] === $login) {
            $user_messages[] = $msg;
        }
    }
    return $user_messages;
}

function saveMessage($login, $name, $email, $message, $rating) {
    $line = implode('|', [
        $login,
        date('Y-m-d H:i:s'),
        $name,
        $email,
        $message,
        $rating
    ]);
    file_put_contents(MESSAGES_FILE, $line . PHP_EOL, FILE_APPEND);
}

function isLoggedIn() {
    return isset($_SESSION['user_login']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return findUserByLogin($_SESSION['user_login']);
}

function validateName($name) {
    if (empty($name)) {
        return 'Имя обязательно';
    }
    if (strlen($name) < 2) {
        return 'Имя должно быть не короче 2 символов';
    }
    if (!preg_match('/^[a-zA-Zа-яА-Я\s-]+$/u', $name)) {
        return 'Имя может содержать только буквы, пробелы и дефисы';
    }
    return null;
}

function validateEmail($email) {
    if (empty($email)) {
        return 'Email обязателен';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Введите корректный email';
    }
    return null;
}

function validateMessage($message) {
    if (empty($message)) {
        return 'Сообщение обязательно';
    }
    if (strlen($message) < 10) {
        return 'Сообщение должно быть не короче 10 символов';
    }
    return null;
}

function validateLogin($login) {
    if (empty($login)) {
        return 'Логин обязателен';
    }
    if (strlen($login) < 3) {
        return 'Логин должен быть не короче 3 символов';
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
        return 'Логин может содержать только буквы, цифры и подчеркивание';
    }
    return null;
}

function validatePassword($password) {
    if (empty($password)) {
        return 'Пароль обязателен';
    }
    if (strlen($password) < 6) {
        return 'Пароль должен быть не короче 6 символов';
    }
    return null;
}