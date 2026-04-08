<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../auth.php';

$boats = $pdo->query("SELECT * FROM boats ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Boats – PrimeBoats Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="d-flex">
    <?php include '../partials/sidebar.php'; ?>
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Boats</h4>
            <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add Boat</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible">
            <?php
            $msgs = [
                'created' => 'Boat added successfully.',
                'updated' => 'Boat updated successfully.',
                'deleted' => 'Boat deleted.',
                'toggled' => 'Rental status updated.',
            ];
            echo htmlspecialchars($msgs[$_GET['msg']] ?? 'Done.');
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (empty($boats)): ?>
        <div class="alert alert-info">No boats yet. <a href="create.php">Add your first boat</a>.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Price/day</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($boats as $boat): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($boat['name']) ?></td>
                        <td>€<?= number_format($boat['price_per_day'], 2) ?></td>
                        <td><?= (int)$boat['capacity'] ?> persons</td>
                        <td>
                            <form method="POST" action="toggle_rented.php" class="d-inline">
                                <input type="hidden" name="id" value="<?= $boat['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $boat['is_rented'] ? 'btn-danger' : 'btn-success' ?>">
                                    <?= $boat['is_rented'] ? '<i class="bi bi-lock me-1"></i>Rented' : '<i class="bi bi-check-circle me-1"></i>Available' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $boat['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="delete.php" class="d-inline" onsubmit="return confirm('Delete this boat? This cannot be undone.')">
                                <input type="hidden" name="id" value="<?= $boat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
