<?php
function json_ok($data): void {
    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $message, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function get_json_body(): array {
    $raw = file_get_contents('php://input');
    return $raw ? (json_decode($raw, true) ?? []) : [];
}

function format_boat(array $b): array {
    $images = $b['images'] ? json_decode($b['images'], true) : [];
    return [
        'id'               => (string)$b['id'],
        'slug'             => (string)($b['slug'] ?? ''),
        'name'             => $b['name'],
        'type'             => $b['type'] ?? '',
        'price'            => (float)$b['price_per_day'],
        'lengthMeters'     => isset($b['length_m'])  && $b['length_m']  !== null ? (float)$b['length_m']  : null,
        'year'             => isset($b['year'])       && $b['year']       !== null ? (int)$b['year']        : null,
        'engine'           => $b['mountable_engine'] ?? '',
        'condition'        => $b['condition_state'] ?? 'used',
        'status'           => $b['status'] ?? 'available',
        'featured'         => (bool)($b['featured'] ?? 0),
        'shortDescription' => $b['short_description'] ?? '',
        'description'      => $b['description'] ?? '',
        'images'           => array_map(fn($f) => UPLOADS_URL . $f, $images),
        'location'         => $b['location'] ?? '',
    ];
}

function format_inquiry(array $i): array {
    return [
        'id'       => (string)$i['id'],
        'name'     => trim(($i['first_name'] ?? '') . ' ' . ($i['last_name'] ?? '')),
        'email'    => $i['email'],
        'phone'    => $i['phone'] ?? '',
        'message'  => $i['message'] ?? '',
        'boatName' => $i['boat_name'] ?? '',
        'status'   => $i['status'] ?? 'new',
        'createdAt'=> $i['created_at'],
    ];
}
