
<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-bold"><i class="bi bi-water me-2"></i>PrimeBoats</h5>
                <p class="text-secondary">Your premier boat rental service in the Netherlands.</p>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?= SITE_URL ?>" class="text-secondary text-decoration-none">Home</a></li>
                    <li><a href="<?= SITE_URL ?>/boats.php" class="text-secondary text-decoration-none">Our Boats</a></li>
                    <li><a href="<?= SITE_URL ?>/booking.php" class="text-secondary text-decoration-none">Book Now</a></li>
                    <li><a href="<?= SITE_URL ?>/contact.php" class="text-secondary text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold">Contact</h6>
                <p class="text-secondary mb-1"><i class="bi bi-envelope me-2"></i><?= CONTACT_EMAIL ?></p>
                <p class="text-secondary mb-1"><i class="bi bi-globe me-2"></i>primeboats.nl</p>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <p class="text-center text-secondary mb-0">&copy; <?= date('Y') ?> PrimeBoats. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
