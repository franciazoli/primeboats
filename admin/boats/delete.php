<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT images FROM boats WHERE id = ?");
$stmt->execute([$id]);
$boat = $stmt->fetch();

if ($boat) {
    $images = json_decode($boat['images'] ?? '[]', true);
    foreach ($images as $img) {
        @unlink(UPLOADS_DIR . $img);
    }
    $pdo->prepare("DELETE FROM boats WHERE id = ?")->execute([$id]);
}

header('Location: index.php?msg=deleted');
exit;
