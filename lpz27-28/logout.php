<?php
// Подключаем функции
require_once 'functions.php';

// Выходим из системы
logoutUser();

// Перенаправляем на главную страницу
header('Location: index.php');
exit();
?>