<?php
declare(strict_types=1);

$cfg = require __DIR__ . '/config.php';
$BASE_DIR = rtrim($cfg['images_base_dir'], DIRECTORY_SEPARATOR);

// 1) Récupère paramètre "p"
$p = $_GET['p'] ?? '';
$p = trim($p, "/\\");

// Sécurité : interdiction du path traversal
if (str_contains($p, '..')) {
    http_response_code(400);
    exit('Invalid path');
}

$p = str_replace('\\', '/', $p);
$p = preg_replace('#/{2,}#', '/', $p);

// 2) Résolution insensible à la casse (utile si stockage Windows)
function resolveCaseInsensitive(string $base, string $rel): ?string {
    $segments = array_filter(explode('/', $rel), 'strlen');
    $current  = $base;
    foreach ($segments as $seg) {
        $list = @scandir($current);
        if ($list === false) return null;
        $match = null;
        foreach ($list as $e) {
            if ($e === '.' || $e === '..') continue;
            if (strcasecmp($e, $seg) === 0) {
                $match = $e;
                break;
            }
        }
        if ($match === null) return null;
        $current = $current . DIRECTORY_SEPARATOR . $match;
    }
    return $current;
}

// 3) Construire candidats possibles
$abs1 = $BASE_DIR . DIRECTORY_SEPARATOR . $p;
$abs2 = $BASE_DIR . DIRECTORY_SEPARATOR . urldecode($p);
$abs3 = resolveCaseInsensitive($BASE_DIR, $p);

$abs = null;
foreach ([$abs1, $abs2, $abs3] as $cand) {
    if ($cand && is_file($cand)) {
        $abs = $cand;
        break;
    }
}

// 4) Mode debug (utile pour voir la résolution)
if (isset($_GET['debug'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'base_dir' => $BASE_DIR,
        'p'        => $p,
        'try'      => [$abs1, $abs2, $abs3],
        'found'    => $abs,
        'exists'   => $abs ? true : false,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// 5) Vérifications sécurité
if (!$abs) {
    http_response_code(404);
    exit('Not found');
}

$root = realpath($BASE_DIR);
if (strpos(realpath(dirname($abs)), $root) !== 0) {
    http_response_code(403);
    exit('Forbidden');
}

// 6) Détermination du type MIME
$ext  = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
$mime = match ($ext) {
    'jpg','jpeg' => 'image/jpeg',
    'png'        => 'image/png',
    'bmp'        => 'image/bmp',
    'gif'        => 'image/gif',
    'webp'       => 'image/webp',
    'svg'        => 'image/svg+xml',
    default      => mime_content_type($abs) ?: 'application/octet-stream'
};

// 7) En-têtes HTTP
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($abs));
header('Cache-Control: public, max-age=31536000, immutable'); // 1 an

// Si c’est juste un HEAD, ne pas envoyer le corps
if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    exit;
}

// 8) Envoi du fichier
readfile($abs);