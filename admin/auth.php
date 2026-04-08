<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/login.php');
    exit;
}
