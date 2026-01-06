<?php
require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['prenom'], $data['nom'], $data['email'], $data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Champs requis manquants.']);
    exit;
}

$prenom = trim($data['prenom']);
$nom = trim($data['nom']);
$email = strtolower(trim($data['email']));
$password = $data['password'];

try {
    $collection = usersCollection();
    $existingUser = $collection->findOne(['email' => $email]);

    if ($existingUser) {
        echo json_encode(['status' => 'error', 'message' => 'Email déjà utilisé.']);
        exit;
    }

    $fullname = ucfirst(strtolower($prenom)) . ' ' . ucfirst(strtolower($nom));
    $username = strtolower($prenom . '.' . $nom);

    $collection->insertOne([
        'prenom' => ucfirst(strtolower($prenom)),
        'nom' => ucfirst(strtolower($nom)),
        'fullname' => $fullname,
        'username' => $username,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => new MongoDB\BSON\UTCDateTime(),
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Utilisateur enregistré avec succès.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
