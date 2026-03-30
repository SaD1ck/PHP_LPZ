<?php
require_once 'functions.php';

session_destroy();

setcookie('remember_login', '', time() - 3600, '/');
setcookie('remember_token', '', time() - 3600, '/');

header('Location: index.php');
exit();
?>