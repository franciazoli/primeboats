<?php
require_once 'includes/config.php';

$pageTitle = 'Contact – PrimeBoats';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name)    $errors[] = 'Your name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
    if (!$message) $errors[] = 'A message is required.';

    if (empty($errors)) {
        $body  = "Contact form message:\n\n";
        $body .= "Name: $name\n";
        $body .= "Email: $email\n";
        $body .= "Subject: $subject\n\n";
        $body .= "Message:\n$message\n";
        mail(CONTACT_EMAIL, "Contact: " . ($subject ?: '(no subject)'), $body, "From: noreply@primeboats.nl\r\nReply-To: $email");
        $success = true;
    }
}

require_once 'includes/header.php';
?>

<div class="container section">
    <div class="row g-5">
        <div class="col-lg-5">
            <h1 class="fw-bold mb-3">Get in Touch</h1>
            <p class="text-secondary mb-4">Have a question or want to learn more? We'd love to hear from you.</p>
            <div class="d-flex align-items-start mb-3">
                <i class="bi bi-envelope fs-5 text-primary me-3 mt-1"></i>
                <div>
                    <strong>Email</strong><br>
                    <a href="mailto:<?= CONTACT_EMAIL ?>" class="text-secondary text-decoration-none"><?= CONTACT_EMAIL ?></a>
                </div>
            </div>
            <div class="d-flex align-items-start mb-3">
                <i class="bi bi-globe fs-5 text-primary me-3 mt-1"></i>
                <div>
                    <strong>Website</strong><br>
                    <span class="text-secondary">primeboats.nl</span>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <?php if ($success): ?>
            <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Message sent! We'll get back to you soon.</div>
            <?php else: ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <form method="POST" class="row g-3">
                <div class="col-6">
                    <label class="form-label">Your Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Message *</label>
                    <textarea name="message" class="form-control" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-5"><i class="bi bi-send me-2"></i>Send Message</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
