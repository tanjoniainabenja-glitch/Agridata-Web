<?php
require 'vendor/autoload.php';

use MongoDB\Client;

header("Content-Type: application/json");

try {
    $client = new Client("mongodb://localhost:27017");
    $collection = $client->prod->users;

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data["email"])) {
        echo json_encode(["status" => "error", "message" => "Pas de données reçues"]);
        exit;
    }

    $data["created_at"] = date("Y-m-d H:i:s");

    $result = $collection->insertOne($data);

    echo json_encode([
        "status" => "success",
        "insertedId" => (string) $result->getInsertedId(),
        "message" => "Insertion MongoDB OK"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Exception : " . $e->getMessage()
    ]);
}
