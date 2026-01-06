<?php
require 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// Headers API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Connexion MongoDB
    $client = new Client("mongodb://localhost:27017");
    
    // CORRECTION ICI : Le nom de la base est 'agridata' selon votre image
    $db = $client->agridata; 

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur connexion : ' . $e->getMessage()]);
    exit;
}

// Récupération des paramètres
$endpoint = $_GET['endpoint'] ?? ''; 
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$inputData = json_decode(file_get_contents('php://input'), true);

// Fonction Historique (Optionnelle, à adapter si vous avez une collection 'historique')
function saveHistorique($db, $userId, $type, $docId) {
    // Si vous avez une collection pour ça, sinon vous pouvez commenter
    // $db->historique->insertOne([...]); 
}

// ---------------------------------------------------------
// 1. ENDPOINT : DATAS (Vos fruits, légumes, etc.)
// Url : api.php?endpoint=datas
// ---------------------------------------------------------
if ($endpoint === 'datas' || $endpoint === 'agriculture') {
    // CORRECTION ICI : La collection s'appelle 'datas' sur l'image
    $collection = $db->datas;

    switch ($method) {
        case 'GET':
            $filter = [];
            
            // Filtre par ID si fourni
            if ($id) {
                $filter['_id'] = new ObjectId($id);
            } 
            // Exemple de filtres basés sur votre image (nom, favori)
            elseif (isset($_GET['nom'])) {
                // Recherche partielle (regex) sur le nom
                $filter['nom'] = new MongoDB\BSON\Regex($_GET['nom'], 'i');
            }

            $docs = $collection->find($filter)->toArray();
            echo json_encode($docs);
            break;

        case 'POST':
            if (empty($inputData)) { echo json_encode(['error' => 'Vide']); break; }
            // Ajout automatique de la date si besoin
            if (!isset($inputData['created_at'])) $inputData['created_at'] = new MongoDB\BSON\UTCDateTime();
            
            $result = $collection->insertOne($inputData);
            echo json_encode(['message' => 'Ajouté dans datas', 'id' => (string)$result->getInsertedId()]);
            break;

        case 'PUT':
            if (!$id) { echo json_encode(['error' => 'ID manquant']); break; }
            $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $inputData]);
            echo json_encode(['message' => 'Mise à jour réussie']);
            break;

        case 'DELETE':
            if (!$id) { echo json_encode(['error' => 'ID manquant']); break; }
            $collection->deleteOne(['_id' => new ObjectId($id)]);
            echo json_encode(['message' => 'Supprimé']);
            break;
    }
    exit;
}

// ---------------------------------------------------------
// 2. ENDPOINT : ELEVAGE (elevage_lots)
// Url : api.php?endpoint=elevage
// ---------------------------------------------------------
if ($endpoint === 'elevage') {
    // CORRECTION ICI : La collection s'appelle 'elevage_lots' sur l'image
    $collection = $db->elevage_lots;

    switch ($method) {
        case 'GET':
            $filter = [];
            if ($id) $filter['_id'] = new ObjectId($id);
            // Autres filtres possibles ici...
            
            $docs = $collection->find($filter)->toArray();
            echo json_encode($docs);
            break;

        case 'POST':
            if (empty($inputData)) { echo json_encode(['error' => 'Vide']); break; }
            $result = $collection->insertOne($inputData);
            echo json_encode(['message' => 'Ajouté dans elevage_lots', 'id' => (string)$result->getInsertedId()]);
            break;

        case 'PUT':
            if (!$id) { echo json_encode(['error' => 'ID manquant']); break; }
            $collection->updateOne(['_id' => new ObjectId($id)], ['$set' => $inputData]);
            echo json_encode(['message' => 'Mise à jour réussie']);
            break;

        case 'DELETE':
            if (!$id) { echo json_encode(['error' => 'ID manquant']); break; }
            $collection->deleteOne(['_id' => new ObjectId($id)]);
            echo json_encode(['message' => 'Supprimé']);
            break;
    }
    exit;
}

// ---------------------------------------------------------
// 3. ENDPOINT : USERS (optionnel, vu sur l'image)
// Url : api.php?endpoint=users
// ---------------------------------------------------------
if ($endpoint === 'users') {
    $collection = $db->users;
    // Logique similaire (GET, POST...)
    if ($method === 'GET') {
        echo json_encode($collection->find()->toArray());
    }
    exit;
}

echo json_encode(['error' => 'Endpoint invalide (essayez ?endpoint=datas)']);
?>