<?php
// index.php
// Affiche les produits à partir de l'API locale http://localhost/example/data.php

// 1) URL de l'API
$apiUrl = 'http://localhost/example/data.php';

// Activer le mode debug avec ?debug=1
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';

// 2) Récupération du JSON côté serveur (cURL)
function fetch_json($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err || $code >= 400 || !$body) {
        return ['__error' => $err ?: "HTTP $code", '__raw' => $body];
    }

    $json = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['__error' => 'JSON invalide: ' . json_last_error_msg(), '__raw' => $body];
    }
    return $json;
}

// 3) Corriger les URLs d’images "10.0.2.2" (émulateur) vers l’hôte courant
function fix_image_url(?string $url): ?string {
    if (!$url) return null;

    $parts = @parse_url($url);
    if (!$parts) return $url;

    $host = $parts['host'] ?? '';
    $path = $parts['path'] ?? '';
    $query = isset($parts['query']) ? ('?' . $parts['query']) : '';

    // Hôte courant et schéma (http/https)
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Si l’image pointe vers 10.0.2.2/localhost/127.0.0.1, on remappe vers l’hôte actuel
    if (in_array($host, ['10.0.2.2', 'localhost', '127.0.0.1'])) {
        return "{$scheme}://{$currentHost}{$path}{$query}";
    }

    // Sinon, on renvoie tel quel
    return $url;
}

// 4) Appel API
$response = fetch_json($apiUrl);
$error = null;
$items = [];

if (isset($response['__error'])) {
    $error = "Impossible de joindre l’API: " . $response['__error'];
} elseif (!isset($response['success']) || !$response['success'] || !isset($response['data']) || !is_array($response['data'])) {
    $error = "Réponse API inattendue.";
} else {
    $items = $response['data'];
}

// Sécurité affichage
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Produits Agridata</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style_pro.css">
</head>
<body>
<header>
    <h1><span class="emoji">🌱</span>Produits Agridata</h1>
</header>

<div class="container">
    <?php if ($error): ?>
        <div class="msg"><?php echo h($error); ?></div>
        <?php if ($debug): ?>
            <div class="debug"><?php
                echo "API URL: {$apiUrl}\n\n";
                echo "Réponse brute:\n";
                echo isset($response['__raw']) ? h($response['__raw']) : h(json_encode($response, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!$error && empty($items)): ?>
        <p class="empty">Aucun produit à afficher.</p>
    <?php endif; ?>

    <?php if (!$error && !empty($items)): ?>
    <div class="grid">
        <?php foreach ($items as $p):
            $name = h($p['nom'] ?? 'Sans nom');
            $desc = h($p['description'] ?? '');
            $img  = fix_image_url($p['image_url'] ?? '');
            $favori = !empty($p['favori']);
            $id = h($p['_id']['$oid'] ?? '');
        ?>
        <article class="card">
            <?php if ($img): ?>
                <img class="thumb" src="<?php echo h($img); ?>" alt="<?php echo $name; ?>"
                     loading="lazy"
                     onerror="this.style.display='none';this.nextElementSibling?.classList.add('no-img');">
            <?php endif; ?>
            <div class="content">
                <h2 class="title"><?php echo $name; ?></h2>
                <p class="desc"><?php echo $desc; ?></p>
                <?php if ($favori): ?>
                    <div class="badge">★ Favori</div>
                <?php endif; ?>
            </div>
            <div class="footer">
                <span>ID: <?php echo $id ?: '—'; ?></span>
                <span>Source: local</span>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>