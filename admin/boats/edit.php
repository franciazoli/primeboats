<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM boats WHERE id = ?");
$stmt->execute([$id]);
$boat = $stmt->fetch();
if (!$boat) { header('Location: index.php'); exit; }

$errors = [];
$existingImages = json_decode($boat['images'] ?? '[]', true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = filter_input(INPUT_POST, 'price_per_day', FILTER_VALIDATE_FLOAT);
    $capacity    = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);
    $length          = filter_input(INPUT_POST, 'length_m', FILTER_VALIDATE_FLOAT) ?: null;
    $weight          = filter_input(INPUT_POST, 'weight_kg', FILTER_VALIDATE_INT) ?: null;
    $loadCapacity    = filter_input(INPUT_POST, 'load_capacity_kg', FILTER_VALIDATE_INT) ?: null;
    $mountableEngine = trim($_POST['mountable_engine'] ?? '') ?: null;

    if (!$name)     $errors[] = 'Name is required.';
    if ($price === false || $price < 0) $errors[] = 'Valid price is required.';
    if (!$capacity || $capacity < 1)    $errors[] = 'Valid capacity is required.';

    // Handle image deletions
    $keepImages = $_POST['keep_images'] ?? [];
    foreach ($existingImages as $img) {
        if (!in_array($img, $keepImages)) {
            @unlink(UPLOADS_DIR . $img);
        }
    }
    $currentImages = array_values(array_intersect($existingImages, $keepImages));

    // Handle new uploads
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $errors[] = 'Only JPG, PNG or WebP images allowed.'; break; }
            $filename = uniqid('boat_', true) . '.' . $ext;
            if (move_uploaded_file($tmpName, UPLOADS_DIR . $filename)) {
                $currentImages[] = $filename;
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE boats SET name=?, description=?, price_per_day=?, capacity=?, length_m=?, weight_kg=?, load_capacity_kg=?, mountable_engine=?, images=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $capacity, $length, $weight, $loadCapacity, $mountableEngine, json_encode($currentImages), $id]);
        header('Location: index.php?msg=updated'); exit;
    }

    $existingImages = $currentImages;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Boat – PrimeBoats Admin</title>
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
            <h4 class="fw-bold mt-2 mb-0">Edit: <?= htmlspecialchars($boat['name']) ?></h4>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-12">
                <label class="form-label">Boat Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? $boat['name']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Asking Price (€) *</label>
                <input type="number" name="price_per_day" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($_POST['price_per_day'] ?? $boat['price_per_day']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Capacity (persons) *</label>
                <input type="number" name="capacity" class="form-control" min="1" value="<?= htmlspecialchars($_POST['capacity'] ?? $boat['capacity']) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Length (m)</label>
                <input type="number" name="length_m" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($_POST['length_m'] ?? $boat['length_m']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Weight (kg)</label>
                <input type="number" name="weight_kg" class="form-control" min="0" value="<?= htmlspecialchars($_POST['weight_kg'] ?? $boat['weight_kg']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Load Capacity (kg)</label>
                <input type="number" name="load_capacity_kg" class="form-control" min="0" value="<?= htmlspecialchars($_POST['load_capacity_kg'] ?? $boat['load_capacity_kg']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Mountable Engine</label>
                <input type="text" name="mountable_engine" class="form-control" placeholder="e.g. 5-25 PS" value="<?= htmlspecialchars($_POST['mountable_engine'] ?? $boat['mountable_engine']) ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? $boat['description']) ?></textarea>
            </div>

            <?php if (!empty($existingImages)): ?>
            <div class="col-12">
                <label class="form-label">Current Photos <small class="text-secondary">(uncheck to delete)</small></label>
                <div class="img-preview-grid">
                    <?php foreach ($existingImages as $img): ?>
                    <div class="text-center">
                        <img src="<?= UPLOADS_URL . htmlspecialchars($img) ?>" alt="">
                        <div class="mt-1">
                            <input type="checkbox" name="keep_images[]" value="<?= htmlspecialchars($img) ?>" checked title="Keep this image">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-12">
                <label class="form-label">Add New Photos</label>
                <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp">
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Save Changes</button>
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
