<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once 'auth.php';

$bookings = $pdo->query("
    SELECT b.*, bo.name AS boat_name
    FROM bookings b
    JOIN boats bo ON b.boat_id = bo.id
    ORDER BY b.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Requests – PrimeBoats Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="d-flex">
    <?php include 'partials/sidebar.php'; ?>
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Booking Requests</h4>
            <span class="badge bg-primary fs-6"><?= count($bookings) ?> total</span>
        </div>

        <?php if (empty($bookings)): ?>
        <p class="text-secondary">No booking requests yet.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Boat</th>
                        <th>Dates</th>
                        <th>Message</th>
                        <th>Received</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $bk): ?>
                    <tr>
                        <td class="text-secondary small"><?= $bk['id'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($bk['first_name'] . ' ' . $bk['last_name']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($bk['email']) ?>"><?= htmlspecialchars($bk['email']) ?></a></td>
                        <td>
                            <?php if ($bk['phone']): ?>
                            <a href="tel:<?= htmlspecialchars($bk['phone']) ?>"><?= htmlspecialchars($bk['phone']) ?></a>
                            <?php else: ?>
                            <span class="text-secondary">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($bk['boat_name']) ?></td>
                        <td class="text-nowrap">
                            <?= htmlspecialchars($bk['start_date']) ?><br>
                            <span class="text-secondary small">→ <?= htmlspecialchars($bk['end_date']) ?></span>
                        </td>
                        <td style="max-width:250px;">
                            <?php if ($bk['message']): ?>
                            <span class="d-inline-block text-truncate" style="max-width:230px;" title="<?= htmlspecialchars($bk['message']) ?>">
                                <?= htmlspecialchars($bk['message']) ?>
                            </span>
                            <a href="#" class="small text-primary ms-1"
                               data-bs-toggle="modal" data-bs-target="#msgModal"
                               data-msg="<?= htmlspecialchars($bk['message']) ?>"
                               data-name="<?= htmlspecialchars($bk['first_name'] . ' ' . $bk['last_name']) ?>">
                                read
                            </a>
                            <?php else: ?>
                            <span class="text-secondary">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary small text-nowrap"><?= date('d M Y, H:i', strtotime($bk['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Message modal -->
<div class="modal fade" id="msgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message from <span id="msgName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="msgBody" class="mb-0" style="white-space:pre-wrap;"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('msgModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('msgName').textContent = btn.dataset.name;
    document.getElementById('msgBody').textContent = btn.dataset.msg;
});
</script>
</body>
</html>
