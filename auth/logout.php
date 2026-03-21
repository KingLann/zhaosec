<?php
session_start();
$redirect = $_GET['redirect'] ?? 'index.php';
session_destroy();
header('Location: ' . $redirect);
exit;
?>
