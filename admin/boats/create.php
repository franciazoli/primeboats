<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = filter_input(INPUT_POST, 'price_per_day', FILTER_VALIDATE_FLOAT);
    $capacity    = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
    $length           = filter_input(INPUT_POST, 'length_m', FILTER_VALIDATE_FLOAT) ?: null;
    $weight           = filter_input(INPUT_POST, 'weight_kg', FILTER_VALIDATE_INT) ?: null;
    $loadCapacity     = filter_input(INPUT_POST, 'load_capacity_kg', FILTER_VALIDATE_INT) ?: null;
    $mountableEngine  = trim($_POST['mountable_engine'] ?? '') ?: null;

    if (!$name)     $errors[] = 'Name is required.';
    if ($price === false || $price < 0) $errors[] = 'Valid price is required.';
    if (!$capacity || $capacity < 1)    $errors[] = 'Valid capacity is required.';

    $uploadedImages = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $errors[] = 'Only JPG, PNG or WebP images allowed.'; break; }
            $filename = uniqid('boat_', true) . '.' . $ext;
            if (move_uploaded_file($tmpName, UPLOADS_DIR . $filename)) {
                $uploadedImages[] = $filename;
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO boats (name, description, price_per_day, capacity, length_m, weight_kg, load_capacity_kg, mountable_engine, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $capacity, $length, $weight, $loadCapacity, $mountableEngine, json_encode($uploadedImages)]);
        header('Location: index.php?msg=created'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Boat – PrimeBoats Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="d-flex">
    <?php include '../partials/sidebar.php'; ?>
    <div class="flex-grow-1 p-4" style="max-width:700px;">
        <div class="mb-4">
            <a href="index.php" class="text-secondary text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Back to boats</a>
            <h4 class="fw-bold mt-2 mb-0">Add New Boat</h4>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-12">
                <label class="form-label">Boat Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Price per Day (€) *</label>
                <input type="number" name="price_per_day" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($_POST['price_per_day'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Capacity (persons) *</label>
                <input type="number" name="capacity" class="form-control" min="1" value="<?= htmlspecialchars($_POST['capacity'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Length (m)</label>
                <input type="number" name="length_m" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($_POST['length_m'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Weight (kg)</label>
                <input type="number" name="weight_kg" class="form-control" min="0" value="<?= htmlspecialchars($_POST['weight_kg'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Load Capacity (kg)</label>
                <input type="number" name="load_capacity_kg" class="form-control" min="0" value="<?= htmlspecialchars($_POST['load_capacity_kg'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Mountable Engine</label>
                <input type="text" name="mountable_engine" class="form-control" placeholder="e.g. 5-25 PS" value="<?= htmlspecialchars($_POST['mountable_engine'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Photos</label>
                <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">You can select multiple images. First image will be used as thumbnail.</div>
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add Boat</button>
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
