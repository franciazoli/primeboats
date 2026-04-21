<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$data = get_json_body();

foreach (['name', 'email', 'message'] as $f) {
    if (empty(trim($data[$f] ?? ''))) json_error("Missing required field: $f");
}
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) json_error('Invalid email address');

$boatId   = isset($data['boatId']) ? (int)$data['boatId'] : null;
$boatName = '';
if ($boatId) {
    $s = $pdo->prepare("SELECT name FROM boats WHERE id = ?");
    $s->execute([$boatId]);
    $boat = $s->fetch();
    if (!$boat) json_error('Boat not found', 404);
    $boatName = $boat['name'];
}

$parts     = explode(' ', trim($data['name']), 2);
$firstName = $parts[0];
$lastName  = $parts[1] ?? '';

$stmt = $pdo->prepare("
    INSERT INTO bookings (boat_id, boat_name, first_name, last_name, email, phone, message, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'new')
");
$stmt->execute([
    $boatId,
    $boatName ?: null,
    htmlspecialchars($firstName),
    htmlspecialchars($lastName),
    filter_var($data['email'], FILTER_SANITIZE_EMAIL),
    htmlspecialchars($data['phone'] ?? ''),
    htmlspecialchars($data['message']),
]);

$subject = $boatName ? "New inquiry: $boatName" : 'New general inquiry';
$body    = "Name: {$data['name']}\nEmail: {$data['email']}\nPhone: " . ($data['phone'] ?? '-')
    . ($boatName ? "\nBoat: $boatName" : '')
    . "\n\nMessage:\n{$data['message']}";
@mail(CONTACT_EMAIL, $subject, $body);

json_ok(['message' => 'Inquiry submitted']);
