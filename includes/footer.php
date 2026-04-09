
<footer class="site-footer text-light py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="fw-bold mb-2"><i class="bi bi-water me-2"></i>PrimeBoats</h5>
                <p class="mb-3" style="color:rgba(255,255,255,0.55);">Your premier boat rental service in the Netherlands. Explore rivers, lakes and canals in style.</p>
                <div class="social-icons d-flex gap-2">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <li><a href="<?= SITE_URL ?>" class="footer-link"><i class="bi bi-chevron-right me-1 small"></i>Home</a></li>
                    <li><a href="<?= SITE_URL ?>/boats.php" class="footer-link"><i class="bi bi-chevron-right me-1 small"></i>Our Boats</a></li>
                    <li><a href="<?= SITE_URL ?>/booking.php" class="footer-link"><i class="bi bi-chevron-right me-1 small"></i>Inquire</a></li>
                    <li><a href="<?= SITE_URL ?>/contact.php" class="footer-link"><i class="bi bi-chevron-right me-1 small"></i>Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-bold mb-3">Contact</h6>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <li><a href="mailto:<?= CONTACT_EMAIL ?>" class="footer-link"><i class="bi bi-envelope me-2"></i><?= CONTACT_EMAIL ?></a></li>
                    <li><span style="color:rgba(255,255,255,0.55);"><i class="bi bi-geo-alt me-2"></i>Netherlands</span></li>
                </ul>
            </div>
        </div>
        <hr class="mt-4" style="border-color:rgba(255,255,255,0.1);">
        <p class="text-center mb-0 small" style="color:rgba(255,255,255,0.35);">&copy; <?= date('Y') ?> PrimeBoats. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
