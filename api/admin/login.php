<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../response.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$data = get_json_body();
if (empty($data['username']) || empty($data['password'])) {
    json_error('Username and password required');
}

$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
$stmt->execute([$data['username']]);
$user = $stmt->fetch();

if (!$user || !password_verify($data['password'], $user['password_hash'])) {
    json_error('Invalid credentials', 401);
}

$_SESSION['admin_id']       = $user['id'];
$_SESSION['admin_username'] = $user['username'];

json_ok(['username' => $user['username']]);
