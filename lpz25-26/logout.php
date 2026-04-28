<?php
require_once 'config.php';

// Вызываем функцию выхода (очищает сессию и cookie)
logoutUser();

// Перенаправляем на главную страницу
header('Location: index.php');
exit();
?>