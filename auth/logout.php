<?php
session_start();
$redirect = $_GET['redirect'] ?? 'index.php';

// 清除JWT Cookie
setcookie('jwt_token', '', time() - 3600, '/');

// 清除session
session_destroy();

header('Location: ' . $redirect);
exit;
?>
