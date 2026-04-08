<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: boats.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM boats WHERE id = ?");
$stmt->execute([$id]);
$boat = $stmt->fetch();

if (!$boat) { header('Location: boats.php'); exit; }

$pageTitle = htmlspecialchars($boat['name']) . ' – PrimeBoats';
$images = json_decode($boat['images'] ?? '[]', true);

require_once 'includes/header.php';
?>

<div class="container section">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="boats.php">Our Boats</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($boat['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Images -->
        <div class="col-lg-7">
            <?php if (!empty($images)): ?>
            <div id="boatCarousel" class="carousel slide rounded overflow-hidden shadow" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($images as $i => $img): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= UPLOADS_URL . htmlspecialchars($img) ?>" class="d-block w-100" style="height:420px;object-fit:cover;" alt="<?= htmlspecialchars($boat['name']) ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#boatCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#boatCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <img src="<?= SITE_URL ?>/assets/images/boat-placeholder.jpg" class="img-fluid rounded shadow" style="height:420px;object-fit:cover;width:100%;" alt="No image available">
            <?php endif; ?>
        </div>

        <!-- Details -->
        <div class="col-lg-5">
            <div class="d-flex align-items-center gap-3 mb-2">
                <h1 class="fw-bold mb-0"><?= htmlspecialchars($boat['name']) ?></h1>
                <?php if ($boat['is_rented']): ?>
                <span class="badge bg-danger">Currently Rented</span>
                <?php else: ?>
                <span class="badge bg-success">Available</span>
                <?php endif; ?>
            </div>

            <p class="display-6 text-primary fw-bold mb-4">€<?= number_format($boat['price_per_day'], 2) ?><small class="fs-6 text-secondary fw-normal">/day</small></p>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <i class="bi bi-people fs-4 text-primary d-block mb-1"></i>
                        <small class="text-secondary">Capacity</small>
                        <div class="fw-bold"><?= (int)$boat['capacity'] ?> persons</div>
                    </div>
                </div>
                <?php if ($boat['length_m']): ?>
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <i class="bi bi-rulers fs-4 text-primary d-block mb-1"></i>
                        <small class="text-secondary">Length</small>
                        <div class="fw-bold"><?= $boat['length_m'] ?> m</div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($boat['weight_kg']): ?>
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <i class="bi bi-box-seam fs-4 text-primary d-block mb-1"></i>
                        <small class="text-secondary">Weight</small>
                        <div class="fw-bold"><?= $boat['weight_kg'] ?> kg</div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($boat['load_capacity_kg']): ?>
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <i class="bi bi-arrow-down-circle fs-4 text-primary d-block mb-1"></i>
                        <small class="text-secondary">Load Capacity</small>
                        <div class="fw-bold"><?= $boat['load_capacity_kg'] ?> kg</div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($boat['mountable_engine']): ?>
                <div class="col-6">
                    <div class="border rounded p-3 text-center">
                        <i class="bi bi-gear fs-4 text-primary d-block mb-1"></i>
                        <small class="text-secondary">Mountable Engine</small>
                        <div class="fw-bold"><?= htmlspecialchars($boat['mountable_engine']) ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($boat['description']): ?>
            <p class="text-secondary mb-4"><?= nl2br(htmlspecialchars($boat['description'])) ?></p>
            <?php endif; ?>

            <?php if (!$boat['is_rented']): ?>
            <a href="booking.php?boat_id=<?= $boat['id'] ?>" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-calendar-plus me-2"></i>Request to Book
            </a>
            <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-clock me-2"></i>This boat is currently rented. Check back soon or <a href="boats.php">view other boats</a>.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
