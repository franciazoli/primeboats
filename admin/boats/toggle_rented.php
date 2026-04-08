<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php'); exit; }

$pdo->prepare("UPDATE boats SET is_rented = NOT is_rented WHERE id = ?")->execute([$id]);

header('Location: index.php?msg=toggled');
exit;
