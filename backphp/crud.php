<?php
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

// HEADERS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// CONNEXION BDD
try {
    $client = new Client("mongodb://localhost:27017");
    $db = $client->agridata;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur BDD : ' . $e->getMessage()]);
    exit;
}

// RECUPERATION DES DONNEES
$endpoint = $_GET['endpoint'] ?? '';
$method   = $_SERVER['REQUEST_METHOD'];
$id       = $_GET['id'] ?? null;
$query    = $_GET['q'] ?? null;
$inputData = json_decode(file_get_contents('php://input'), true);

if ($endpoint === 'datas') {
    $collection = $db->datas;

    switch ($method) {
        case 'GET':
            $filter = [];
            if ($id) {
                $filter['_id'] = new ObjectId($id);
            } elseif ($query) {
                $filter['nom'] = ['$regex' => $query, '$options' => 'i'];
            }
            $docs = $collection->find($filter)->toArray();
            // On convertit _id en id pour JS
            $result = array_map(function($item){
                return [
                    'id' => (string)$item['_id'],
                    'nom' => $item['nom'] ?? '',
                    'description' => $item['description'] ?? '',
                    'image_url' => $item['image_url'] ?? ''
                ];
            }, $docs);
            echo json_encode($result);
            break;

        case 'POST':
            if (empty($inputData)) {
                echo json_encode(['error' => 'Aucune donnée reçue']);
                break;
            }
            $inputData['created_at'] = new MongoDB\BSON\UTCDateTime();
            $result = $collection->insertOne($inputData);
            echo json_encode(['message'=>'Produit ajouté','id'=>(string)$result->getInsertedId()]);
            break;

        case 'PUT':
            if (!$id) { echo json_encode(['error'=>'ID manquant']); break; }
            $collection->updateOne(['_id'=>new ObjectId($id)], ['$set'=>$inputData]);
            echo json_encode(['message'=>'Produit mis à jour']);
            break;

        case 'DELETE':
            if (!$id) { echo json_encode(['error'=>'ID manquant']); break; }
            $collection->deleteOne(['_id'=>new ObjectId($id)]);
            echo json_encode(['message'=>'Produit supprimé']);
            break;
    }
    exit;
}

echo json_encode(['error'=>'Mauvais endpoint']);
?>
