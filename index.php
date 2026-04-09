<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$pageTitle = 'PrimeBoats – Boats for Sale in the Netherlands';

$featured = $pdo->query("SELECT * FROM boats WHERE is_rented = 0 ORDER BY created_at DESC LIMIT 3")->fetchAll();

require_once 'includes/header.php';
?>

<!-- Hero -->
<section class="hero text-white text-center">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3">Find Your Perfect Boat</h1>
        <p class="lead mb-4 fs-5">Browse our selection of quality boats for sale in the Netherlands. Private and commercial vessels available.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-2">
            <a href="boats.php" class="btn btn-primary btn-lg px-4">View Our Boats</a>
            <a href="booking.php" class="btn btn-outline-light btn-lg px-4">Inquire Now</a>
        </div>
    </div>
    <a href="#content" class="hero-scroll">
        <span>Scroll</span>
        <i class="bi bi-chevron-down fs-5"></i>
    </a>
</section>

<!-- Features -->
<section id="content" class="section bg-light">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4">
                    <i class="bi bi-shield-check display-5 text-primary mb-3 d-block"></i>
                    <h5 class="fw-bold">Verified Vessels</h5>
                    <p class="text-secondary">Every boat in our fleet is thoroughly inspected and comes with complete documentation.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="bi bi-chat-dots display-5 text-primary mb-3 d-block"></i>
                    <h5 class="fw-bold">Personal Service</h5>
                    <p class="text-secondary">We guide you through the entire purchase process, from inquiry to handover.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <i class="bi bi-geo-alt display-5 text-primary mb-3 d-block"></i>
                    <h5 class="fw-bold">Great Locations</h5>
                    <p class="text-secondary">Based in the Netherlands with boats across beautiful rivers, lakes and canals.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Boats -->
<?php if (!empty($featured)): ?>
<section class="section">
    <div class="container">
        <h2 class="fw-bold text-center mb-2">Boats for Sale</h2>
        <p class="text-center text-secondary mb-5">A selection from our current inventory</p>
        <div class="row g-4">
            <?php foreach ($featured as $boat): ?>
            <?php
                $images = json_decode($boat['images'] ?? '[]', true);
                $thumb = !empty($images) ? UPLOADS_URL . $images[0] : SITE_URL . '/assets/images/boat-placeholder.jpg';
            ?>
            <div class="col-md-4">
                <div class="card boat-card h-100">
                    <a href="boat.php?id=<?= $boat['id'] ?>" class="card-img-wrapper text-decoration-none">
                        <img src="<?= htmlspecialchars($thumb) ?>" alt="<?= htmlspecialchars($boat['name']) ?>">
                        <div class="card-img-overlay-hover"><span>View Details</span></div>
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($boat['name']) ?></h5>
                        <p class="card-text text-secondary small flex-grow-1"><?= nl2br(htmlspecialchars(substr($boat['description'] ?? '', 0, 100))) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold text-primary">€<?= number_format($boat['price_per_day'], 0, ',', '.') ?></span>
                            <a href="boat.php?id=<?= $boat['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="boats.php" class="btn btn-primary px-5">See All Boats</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="section bg-dark text-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Ready to Make a Move?</h2>
        <p class="text-secondary mb-4 fs-5">Contact us today and we'll help you find the perfect boat.</p>
        <a href="booking.php" class="btn btn-primary btn-lg px-5">Send an Inquiry</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
