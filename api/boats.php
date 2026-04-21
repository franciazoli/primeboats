<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);

$id   = isset($_GET['id'])   ? (int)$_GET['id']    : null;
$slug = isset($_GET['slug']) ? trim($_GET['slug'])  : null;

if ($id || $slug) {
    $stmt = $id
        ? $pdo->prepare("SELECT * FROM boats WHERE id = ?")
        : $pdo->prepare("SELECT * FROM boats WHERE slug = ?");
    $stmt->execute([$id ?? $slug]);
    $boat = $stmt->fetch();
    if (!$boat) json_error('Boat not found', 404);
    json_ok(format_boat($boat));
}

$wheres = [];
$params = [];

if (!empty($_GET['type'])) {
    $wheres[] = 'type = ?';
    $params[] = $_GET['type'];
}
if (!empty($_GET['status'])) {
    $wheres[] = 'status = ?';
    $params[] = $_GET['status'];
}
if (!empty($_GET['search'])) {
    $wheres[] = '(name LIKE ? OR short_description LIKE ?)';
    $q = '%' . $_GET['search'] . '%';
    $params[] = $q;
    $params[] = $q;
}
if (!empty($_GET['featured'])) {
    $wheres[] = 'featured = 1';
}

$where = $wheres ? 'WHERE ' . implode(' AND ', $wheres) : '';
$stmt  = $pdo->prepare("SELECT * FROM boats $where ORDER BY created_at DESC");
$stmt->execute($params);

json_ok(array_map('format_boat', $stmt->fetchAll()));
