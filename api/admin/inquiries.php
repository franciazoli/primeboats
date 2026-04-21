<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../response.php';

session_start();
if (!isset($_SESSION['admin_id'])) json_error('Unauthorized', 401);

$method = $_SERVER['REQUEST_METHOD'];

// --- GET: list ---
if ($method === 'GET') {
    $filter = $_GET['status'] ?? 'all';
    $wheres = [];
    $params = [];
    if ($filter !== 'all' && in_array($filter, ['new', 'contacted', 'archived'], true)) {
        $wheres[] = 'status = ?';
        $params[] = $filter;
    }
    $where = $wheres ? 'WHERE ' . implode(' AND ', $wheres) : '';
    $stmt  = $pdo->prepare("SELECT * FROM bookings $where ORDER BY created_at DESC");
    $stmt->execute($params);
    json_ok(array_map('format_inquiry', $stmt->fetchAll()));
}

// --- PATCH: update status ---
if ($method === 'PATCH') {
    $data   = get_json_body();
    $id     = (int)($data['id'] ?? 0);
    $status = $data['status'] ?? '';
    if (!$id || !in_array($status, ['new', 'contacted', 'archived'], true)) {
        json_error('Invalid request');
    }
    $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$status, $id]);
    json_ok(['id' => (string)$id, 'status' => $status]);
}

// --- DELETE ---
if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) json_error('Missing id');
    $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
    json_ok(['deleted' => $id]);
}

json_error('Method not allowed', 405);
