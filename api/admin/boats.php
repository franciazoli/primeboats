<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../response.php';

session_start();
if (!isset($_SESSION['admin_id'])) json_error('Unauthorized', 401);

$method = $_SERVER['REQUEST_METHOD'];

// --- GET: list all boats ---
if ($method === 'GET') {
    $wheres = [];
    $params = [];
    if (!empty($_GET['search'])) {
        $wheres[] = 'name LIKE ?';
        $params[] = '%' . $_GET['search'] . '%';
    }
    $where = $wheres ? 'WHERE ' . implode(' AND ', $wheres) : '';
    $stmt  = $pdo->prepare("SELECT * FROM boats $where ORDER BY created_at DESC");
    $stmt->execute($params);
    json_ok(array_map('format_boat', $stmt->fetchAll()));
}

// --- POST: create boat ---
if ($method === 'POST') {
    $data = get_json_body();
    if (empty($data['name'])) json_error('Name is required');

    $slug = !empty($data['slug']) ? $data['slug'] : make_slug($data['name'], $pdo);

    $stmt = $pdo->prepare("
        INSERT INTO boats
            (slug, name, type, price_per_day, length_m, year, mountable_engine,
             condition_state, status, featured, short_description, description, location, images)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");
    $stmt->execute([
        $slug,
        $data['name'],
        $data['type']             ?? null,
        $data['price']            ?? 0,
        $data['lengthMeters']     ?? null,
        $data['year']             ?? null,
        $data['engine']           ?? null,
        $data['condition']        ?? 'used',
        $data['status']           ?? 'available',
        empty($data['featured'])  ? 0 : 1,
        $data['shortDescription'] ?? null,
        $data['description']      ?? null,
        $data['location']         ?? null,
        json_encode(extract_filenames($data['images'] ?? [])),
    ]);

    $id   = (int)$pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM boats WHERE id = ?");
    $stmt->execute([$id]);
    json_ok(format_boat($stmt->fetch()));
}

// --- PUT: update boat ---
if ($method === 'PUT') {
    $data = get_json_body();
    $id   = (int)($data['id'] ?? 0);
    if (!$id) json_error('Missing boat id');
    if (empty($data['name'])) json_error('Name is required');

    $stmt = $pdo->prepare("
        UPDATE boats SET
            slug = ?, name = ?, type = ?, price_per_day = ?, length_m = ?, year = ?,
            mountable_engine = ?, condition_state = ?, status = ?, featured = ?,
            short_description = ?, description = ?, location = ?, images = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $data['slug']             ?? null,
        $data['name'],
        $data['type']             ?? null,
        $data['price']            ?? 0,
        $data['lengthMeters']     ?? null,
        $data['year']             ?? null,
        $data['engine']           ?? null,
        $data['condition']        ?? 'used',
        $data['status']           ?? 'available',
        empty($data['featured'])  ? 0 : 1,
        $data['shortDescription'] ?? null,
        $data['description']      ?? null,
        $data['location']         ?? null,
        json_encode(extract_filenames($data['images'] ?? [])),
        $id,
    ]);

    $stmt = $pdo->prepare("SELECT * FROM boats WHERE id = ?");
    $stmt->execute([$id]);
    json_ok(format_boat($stmt->fetch()));
}

// --- DELETE ---
if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (!$id) json_error('Missing boat id');

    $stmt = $pdo->prepare("SELECT images FROM boats WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['images']) {
        foreach (json_decode($row['images'], true) ?? [] as $f) {
            $path = UPLOADS_DIR . $f;
            if (file_exists($path)) @unlink($path);
        }
    }

    $pdo->prepare("DELETE FROM boats WHERE id = ?")->execute([$id]);
    json_ok(['deleted' => $id]);
}

json_error('Method not allowed', 405);

// --- Helpers ---

function make_slug(string $name, PDO $pdo): string {
    $base = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    $slug = $base;
    $i    = 1;
    while (true) {
        $s = $pdo->prepare("SELECT id FROM boats WHERE slug = ?");
        $s->execute([$slug]);
        if (!$s->fetch()) break;
        $slug = "$base-$i";
        $i++;
    }
    return $slug;
}

// Images arrive as full URLs from the frontend — strip the uploads base to store only filenames.
function extract_filenames(array $images): array {
    return array_values(array_filter(array_map(function ($url) {
        if (str_starts_with($url, UPLOADS_URL)) {
            return substr($url, strlen(UPLOADS_URL));
        }
        // Already a bare filename (no slash)
        if (!str_contains($url, '/')) return $url;
        return null;
    }, $images)));
}
