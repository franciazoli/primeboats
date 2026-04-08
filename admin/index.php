<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once 'auth.php';

$totalBoats    = $pdo->query("SELECT COUNT(*) FROM boats")->fetchColumn();
$rentedBoats   = $pdo->query("SELECT COUNT(*) FROM boats WHERE is_rented = 1")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$recentBookings = $pdo->query("SELECT b.*, bo.name AS boat_name FROM bookings b JOIN boats bo ON b.boat_id = bo.id ORDER BY b.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – PrimeBoats</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">
    <?php include 'partials/sidebar.php'; ?>
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Dashboard</h4>
            <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>View Site</a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded p-3"><i class="bi bi-water fs-4 text-primary"></i></div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $totalBoats ?></div>
                            <div class="text-secondary small">Total Boats</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 rounded p-3"><i class="bi bi-lock fs-4 text-danger"></i></div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $rentedBoats ?></div>
                            <div class="text-secondary small">Currently Rented</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 rounded p-3"><i class="bi bi-calendar-check fs-4 text-success"></i></div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $totalBookings ?></div>
                            <div class="text-secondary small">Total Booking Requests</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3">Recent Booking Requests</h5>
        <?php if (empty($recentBookings)): ?>
        <p class="text-secondary">No booking requests yet.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>Name</th><th>Boat</th><th>Dates</th><th>Email</th><th>Received</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $bk): ?>
                    <tr>
                        <td><?= htmlspecialchars($bk['first_name'] . ' ' . $bk['last_name']) ?></td>
                        <td><?= htmlspecialchars($bk['boat_name']) ?></td>
                        <td><?= $bk['start_date'] ?> → <?= $bk['end_date'] ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($bk['email']) ?>"><?= htmlspecialchars($bk['email']) ?></a></td>
                        <td class="text-secondary small"><?= date('d M Y', strtotime($bk['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
