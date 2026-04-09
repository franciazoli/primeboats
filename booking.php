<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$pageTitle = 'Inquire About a Boat – PrimeBoats';

$boats = $pdo->query("SELECT id, name FROM boats WHERE is_rented = 0 ORDER BY name")->fetchAll();

$preselected = filter_input(INPUT_GET, 'boat_id', FILTER_VALIDATE_INT);

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName  = trim($_POST['first_name'] ?? '');
    $lastName   = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $boatId     = filter_input(INPUT_POST, 'boat_id', FILTER_VALIDATE_INT);
    $message    = trim($_POST['message'] ?? '');

    if (!$firstName) $errors[] = 'First name is required.';
    if (!$lastName)  $errors[] = 'Last name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
    if (!$boatId)    $errors[] = 'Please select a boat.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO bookings (boat_id, first_name, last_name, email, phone, start_date, end_date, message) VALUES (?, ?, ?, ?, ?, NULL, NULL, ?)");
        $stmt->execute([$boatId, $firstName, $lastName, $email, $phone, $message]);

        $boatName = '';
        foreach ($boats as $b) { if ($b['id'] == $boatId) $boatName = $b['name']; }
        $subject = "New Sales Inquiry – $boatName";
        $body  = "New inquiry received:\n\n";
        $body .= "Name: $firstName $lastName\n";
        $body .= "Email: $email\n";
        $body .= "Phone: $phone\n";
        $body .= "Boat: $boatName\n";
        $body .= "Message: $message\n";
        mail(CONTACT_EMAIL . ', sales@primeboats.nl', $subject, $body, "From: noreply@primeboats.nl");

        $success = true;
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="max-width:640px; padding-top:60px; padding-bottom:80px;">
    <h1 class="fw-bold mb-2">Inquire About a Boat</h1>
    <p class="text-secondary mb-5">Interested in one of our boats? Fill in the form and we'll get back to you as soon as possible.</p>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i><strong>Inquiry sent!</strong> We'll contact you shortly.
    </div>
    <?php else: ?>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST">
        <div class="row g-3">
            <div class="col-6">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
            </div>
            <div class="col-6">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
            </div>
            <div class="col-6">
                <label class="form-label">Email Address *</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="col-6">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Select Boat *</label>
                <select name="boat_id" class="form-select" required>
                    <option value="">-- Choose a boat --</option>
                    <?php foreach ($boats as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= (($preselected == $b['id']) || (($_POST['boat_id'] ?? '') == $b['id'])) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Any questions about the boat, viewing arrangements, etc."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </div>
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-send me-2"></i>Send Inquiry
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
