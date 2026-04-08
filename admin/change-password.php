<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once 'auth.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current  = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $user = $stmt->fetch();

    if (!password_verify($current, $user['password_hash'])) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?")->execute([$hash, $_SESSION['admin_id']]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password – PrimeBoats Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">
    <?php include 'partials/sidebar.php'; ?>
    <div class="flex-grow-1 p-4" style="max-width:480px;">
        <h4 class="fw-bold mb-4">Change Password</h4>

        <?php if ($success): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Password changed successfully.</div>
        <?php else: ?>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="row g-3">
            <div class="col-12">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="8">
            </div>
            <div class="col-12">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-lock me-2"></i>Change Password</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
