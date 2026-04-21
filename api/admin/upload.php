<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../response.php';

session_start();
if (!isset($_SESSION['admin_id'])) json_error('Unauthorized', 401);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);
if (empty($_FILES['image'])) json_error('No file uploaded');

$file    = $_FILES['image'];
$allowed = ['image/jpeg', 'image/png', 'image/webp'];

if ($file['error'] !== UPLOAD_ERR_OK) json_error('Upload error');
if (!in_array($file['type'], $allowed, true)) json_error('Only JPG, PNG and WebP allowed');
if ($file['size'] > 5 * 1024 * 1024) json_error('Max file size is 5 MB');

$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'boat_' . uniqid() . '.' . $ext;
$dest     = UPLOADS_DIR . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) json_error('Failed to save file');

json_ok(['filename' => $filename, 'url' => UPLOADS_URL . $filename]);
