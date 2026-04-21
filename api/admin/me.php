<?php
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../response.php';

session_start();

if (!isset($_SESSION['admin_id'])) json_error('Unauthorized', 401);

json_ok(['username' => $_SESSION['admin_username']]);
