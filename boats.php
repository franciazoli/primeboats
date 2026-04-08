<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$pageTitle = 'Our Boats – PrimeBoats';

$boats = $pdo->query("SELECT * FROM boats ORDER BY is_rented ASC, name ASC")->fetchAll();

require_once 'includes/header.php';
?>

<div class="container section">
    <h1 class="fw-bold mb-2">Our Boats</h1>
    <p class="text-secondary mb-5">Browse our full fleet. Availability is updated in real time.</p>

    <?php if (empty($boats)): ?>
        <div class="alert alert-info">No boats available at the moment. Check back soon!</div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($boats as $boat): ?>
        <?php
            $images = json_decode($boat['images'] ?? '[]', true);
            $thumb = !empty($images) ? UPLOADS_URL . $images[0] : SITE_URL . '/assets/images/boat-placeholder.jpg';
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card boat-card h-100 <?= $boat['is_rented'] ? 'opacity-75' : '' ?>">
                <a href="boat.php?id=<?= $boat['id'] ?>" class="card-img-wrapper text-decoration-none">
                    <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($boat['name']) ?>">
                    <?php if (!$boat['is_rented']): ?>
                    <div class="card-img-overlay-hover"><span>View & Book</span></div>
                    <?php endif; ?>
                    <?php if ($boat['is_rented']): ?>
                    <span class="position-absolute top-0 end-0 m-2 badge badge-rented" style="z-index:2;">Currently Rented</span>
                    <?php endif; ?>
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($boat['name']) ?></h5>
                    <p class="card-text text-secondary small flex-grow-1"><?= nl2br(htmlspecialchars(substr($boat['description'] ?? '', 0, 120))) ?>...</p>
                    <div class="row g-2 text-secondary small mb-3">
                        <div class="col-6"><i class="bi bi-people me-1"></i><?= (int)$boat['capacity'] ?> persons</div>
                        <?php if ($boat['length_m']): ?>
                        <div class="col-6"><i class="bi bi-rulers me-1"></i><?= $boat['length_m'] ?>m</div>
                        <?php endif; ?>
                        <?php if ($boat['year']): ?>
                        <div class="col-6"><i class="bi bi-calendar me-1"></i><?= $boat['year'] ?></div>
                        <?php endif; ?>
                        <div class="col-6 fw-bold text-primary">€<?= number_format($boat['price_per_day'], 2) ?>/day</div>
                    </div>
                    <a href="boat.php?id=<?= $boat['id'] ?>" class="btn btn-outline-primary btn-sm mt-auto">
                        <?= $boat['is_rented'] ? 'View Details' : 'View & Book' ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
