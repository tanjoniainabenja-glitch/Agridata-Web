<?php
// api_cartes.php
// Renvoie la liste des cartes (JSON) avec filtres ?province=&type=&q=
// et normalise les chemins d'images vers http://localhost/Agridata/Cart_Image/...

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // optionnel

// ===== CONFIG =======================================================
$MONGO_URI   = getenv('MONGO_URI')        ?: 'mongodb://127.0.0.1:27017';
$DB_NAME     = getenv('MONGO_DB')         ?: 'agridata';
$COLLECTION  = getenv('MONGO_COLLECTION') ?: 'maps';
$BASE_URL    = getenv('BASE_URL')         ?: 'http://localhost';
// ====================================================================

// 1) Autoload Composer
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
  http_response_code(500);
  echo json_encode([
    'error' => "Composer autoload manquant. Exécute: composer require mongodb/mongodb"
  ], JSON_UNESCAPED_UNICODE);
  exit;
}
require $autoload;

use MongoDB\Client;

// 2) Connexion MongoDB
try {
  $client = new Client($MONGO_URI);
  $col    = $client->selectDatabase($DB_NAME)->selectCollection($COLLECTION);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Connexion MongoDB échouée: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
  exit;
}

// 3) Récupération des filtres
$province = $_GET['province'] ?? '';
$type     = $_GET['type'] ?? '';
$q        = $_GET['q'] ?? '';

$filter = [];
if ($province !== '') $filter['province'] = $province;
if ($type !== '')     $filter['type']     = $type;
if ($q !== '')        $filter['nom']      = ['$regex' => $q, '$options' => 'i'];

// 4) Lecture en base
try {
  $cursor = $col->find($filter, [
    'sort' => ['province' => 1, 'type' => 1, 'nom' => 1],
    'projection' => ['nom' => 1, 'province' => 1, 'type' => 1, 'chemin_image' => 1]
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Lecture MongoDB échouée: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
  exit;
}

// 5) Conversion en URL publique
function toPublicUrl(string $chemin, string $baseUrl): string {
    // Si déjà une URL absolue : retour direct
    if (preg_match('#^https?://#i', $chemin)) {
        return $chemin;
    }

    $p = str_replace('\\', '/', $chemin);
    $p = ltrim($p, '/');

    // Normalise "Cart Image" ou "Cart_Image"
    $p = preg_replace('#^Agridata/(Cart Image|Cart_Image)/#i', 'Agridata/Cart_Image/', $p);
    $p = preg_replace('#/{2,}#', '/', $p);

    // Encode les segments individuellement (mais pas le domaine)
    $segments = array_map('rawurlencode', explode('/', $p));

    return rtrim($baseUrl, '/') . '/' . implode('/', $segments);
}

// 6) Construction de la réponse
$out = [];
foreach ($cursor as $doc) {
  $raw = isset($doc['chemin_image']) ? (string)$doc['chemin_image'] : '';
  $out[] = [
    'nom'          => (string)($doc['nom']      ?? ''),
    'province'     => (string)($doc['province'] ?? ''),
    'type'         => (string)($doc['type']     ?? ''),
    'chemin_image' => $raw,
    'urlImage'     => toPublicUrl($raw, $BASE_URL),
  ];
}

// 7) Sortie JSON
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);