<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_POST['submitted'])) {
    header('Location: index.php');
    exit();
}

$errors = [];

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$birthdate = trim($_POST['birthdate'] ?? '');
$zipcode = trim($_POST['zipcode'] ?? '');
$city = trim($_POST['city'] ?? '');
$snils = trim($_POST['snils'] ?? '');

$_SESSION['form_data'] = [
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'birthdate' => $birthdate,
    'zipcode' => $zipcode,
    'city' => $city,
    'snils' => $snils
];

if (empty($name)) {
    $errors['name'] = 'Имя обязательно для заполнения';
} elseif (!preg_match('/^[a-zA-Zа-яА-Я\s-]{2,30}$/u', $name)) {
    $errors['name'] = 'Имя должно содержать только буквы, пробелы или дефисы (2-30 символов)';
}

if (empty($email)) {
    $errors['email'] = 'Email обязателен для заполнения';
} elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
    $errors['email'] = 'Введите корректный email адрес';
}

if (empty($phone)) {
    $errors['phone'] = 'Телефон обязателен для заполнения';
} elseif (!preg_match('/^\+7\s\d{3}\s\d{3}-\d{2}-\d{2}$/', $phone)) {
    $errors['phone'] = 'Телефон должен быть в формате +7 999 123-45-67';
}

if (empty($password)) {
    $errors['password'] = 'Пароль обязателен для заполнения';
} elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    $errors['password'] = 'Пароль: минимум 8 символов, буквы в обоих регистрах, цифры и спецсимволы (@$!%*?&)';
}

if ($password !== $confirm_password) {
    $errors['confirm_password'] = 'Пароли не совпадают';
}

if (empty($birthdate)) {
    $errors['birthdate'] = 'Дата рождения обязательна';
} elseif (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $birthdate)) {
    $errors['birthdate'] = 'Дата должна быть в формате ДД.ММ.ГГГГ';
} else {
    $parts = explode('.', $birthdate);
    $day = (int)$parts[0];
    $month = (int)$parts[1];
    $year = (int)$parts[2];
    if (!checkdate($month, $day, $year)) {
        $errors['birthdate'] = 'Укажите корректную дату';
    } else {
        $birth_timestamp = strtotime($birthdate);
        $age = (time() - $birth_timestamp) / (365 * 24 * 60 * 60);
        if ($age < 18) {
            $errors['birthdate'] = 'Вам должно быть не менее 18 лет';
        }
    }
}

if (empty($zipcode)) {
    $errors['zipcode'] = 'Почтовый индекс обязателен';
} elseif (!preg_match('/^\d{6}$/', $zipcode)) {
    $errors['zipcode'] = 'Индекс должен содержать ровно 6 цифр';
}

if (empty($city)) {
    $errors['city'] = 'Город обязателен для заполнения';
} elseif (!preg_match('/^[a-zA-Zа-яА-Я\s-]{2,50}$/u', $city)) {
    $errors['city'] = 'Город должен содержать только буквы, пробелы или дефисы (2-50 символов)';
}

if (empty($snils)) {
    $errors['snils'] = 'СНИЛС обязателен для заполнения';
} elseif (!preg_match('/^\d{3}-\d{3}-\d{3} \d{2}$/', $snils)) {
    $errors['snils'] = 'СНИЛС должен быть в формате XXX-XXX-XXX XX';
}

if (empty($errors)) {
    unset($_SESSION['form_data']);
    $_SESSION['registered_email'] = $email;
    $_SESSION['registered_name'] = $name;
    header('Location: success.php');
    exit();
} else {
    $_SESSION['errors'] = $errors;
    header('Location: index.php');
    exit();
}
?>