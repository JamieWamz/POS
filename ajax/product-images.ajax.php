<?php

require_once __DIR__ . '/../core/bootstrap.php';

require_method('POST');
require_auth(['Administrator', 'Special']);
require_csrf();
header('Content-Type: application/json; charset=utf-8');

$query = trim((string) ($_POST['query'] ?? ''));
if (mb_strlen($query) < 2 || mb_strlen($query) > 120) {
    abort_request(422, 'Enter a product name between 2 and 120 characters.');
}

$cacheKey = hash('sha256', mb_strtolower($query));
$cached = $_SESSION['product_image_search_cache'] ?? null;
if (is_array($cached)
    && ($cached['key'] ?? '') === $cacheKey
    && (int) ($cached['expires'] ?? 0) > time()
    && isset($cached['results'])
    && is_array($cached['results'])) {
    echo json_encode(['results' => $cached['results'], 'source' => 'Open Food Facts'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$lastSearchAt = (float) ($_SESSION['product_image_search_at'] ?? 0);
if (microtime(true) - $lastSearchAt < 0.5) {
    abort_request(429, 'Please wait a moment before searching again.');
}
$_SESSION['product_image_search_at'] = microtime(true);

$searchVariants = [$query];
$withoutPackSize = preg_replace('/\b\d+\s*[x×]\s*/iu', '', $query);
$withoutPackSize = preg_replace('/\b\d+(?:[.,]\d+)?\s*(?:ml|cl|litres?|liters?|l|g|kg|packs?|pk|pcs?|pieces?)\b/iu', '', (string) $withoutPackSize);
$withoutPackSize = trim((string) preg_replace('/\s+/u', ' ', (string) $withoutPackSize));
if (mb_strlen($withoutPackSize) >= 2 && !in_array($withoutPackSize, $searchVariants, true)) {
    $searchVariants[] = $withoutPackSize;
}
$tokens = preg_split('/\s+/u', $withoutPackSize !== '' ? $withoutPackSize : $query, -1, PREG_SPLIT_NO_EMPTY);
$brandToken = is_array($tokens) ? trim((string) ($tokens[0] ?? '')) : '';
if (mb_strlen($brandToken) >= 3 && !in_array($brandToken, $searchVariants, true)) {
    $searchVariants[] = $brandToken;
}
while (count($searchVariants) < 3) {
    $searchVariants[] = $searchVariants[count($searchVariants) - 1];
}
$searchVariants = array_slice($searchVariants, 0, 3);

$extractResults = static function (array $products): array {
    $matches = [];
    $seen = [];
    foreach ($products as $product) {
        if (!is_array($product)) {
            continue;
        }
        $image = trim((string) ($product['image_front_url'] ?? $product['image_url'] ?? ''));
        $parts = parse_url($image);
        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($image === ''
            || !is_array($parts)
            || strtolower((string) ($parts['scheme'] ?? '')) !== 'https'
            || !in_array($host, ['images.openfoodfacts.org', 'static.openfoodfacts.org'], true)
            || isset($seen[$image])) {
            continue;
        }
        $seen[$image] = true;
        $name = trim((string) ($product['product_name'] ?? ''));
        $brand = trim((string) ($product['brands'] ?? ''));
        $quantity = trim((string) ($product['quantity'] ?? ''));
        $code = preg_replace('/\D+/', '', (string) ($product['code'] ?? ''));
        $matches[] = [
            'name' => $name !== '' ? $name : ($brand !== '' ? $brand : 'Product image'),
            'brand' => $brand,
            'quantity' => $quantity,
            'image' => $image,
            'product_url' => $code !== '' ? 'https://world.openfoodfacts.org/product/' . $code : 'https://world.openfoodfacts.org/',
        ];
        if (count($matches) >= 8) {
            break;
        }
    }
    return $matches;
};

$results = [];
$receivedResponse = false;
$receivedValidPayload = false;
foreach ($searchVariants as $searchTerm) {
    $parameters = http_build_query([
        'search_terms' => $searchTerm,
        'search_simple' => 1,
        'action' => 'process',
        'json' => 1,
        'page_size' => 8,
        'fields' => 'code,product_name,brands,quantity,image_front_url,image_url',
    ], '', '&', PHP_QUERY_RFC3986);
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 18,
            'ignore_errors' => true,
            'header' => "Accept: application/json\r\nUser-Agent: GoldenTapPOS/1.0 (https://github.com/JamieWamz/POS)\r\n",
        ],
    ]);
    $response = @file_get_contents('https://world.openfoodfacts.org/cgi/search.pl?' . $parameters, false, $context);
    if ($response === false) {
        continue;
    }
    $receivedResponse = true;
    $decoded = json_decode($response, true);
    if (is_array($decoded) && isset($decoded['products']) && is_array($decoded['products'])) {
        $receivedValidPayload = true;
        $results = $extractResults($decoded['products']);
        if ($results !== []) {
            break;
        }
    }
}

if (!$receivedValidPayload) {
    if (!$receivedResponse) {
        abort_request(502, 'The product image catalogue is temporarily unavailable. Upload an image or try again.');
    }
    abort_request(502, 'The product image catalogue returned an invalid response.');
}

$_SESSION['product_image_search_cache'] = [
    'key' => $cacheKey,
    'expires' => time() + 300,
    'results' => $results,
];

echo json_encode(['results' => $results, 'source' => 'Open Food Facts'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
