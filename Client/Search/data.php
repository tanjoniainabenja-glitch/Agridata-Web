<?php
header('Content-Type: application/json');

// Connexion à MongoDB (localhost par défaut)
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Préparer la requête sur la collection agridata.datas
$query = new MongoDB\Driver\Query([]); // [] = sans filtre
$cursor = $manager->executeQuery("agridata.datas", $query);

$data = [];
foreach ($cursor as $document) {
    $data[] = $document;
}

// Réponse JSON
echo json_encode([
    "success" => true,
    "data" => $data
], JSON_PRETTY_PRINT);
?>
