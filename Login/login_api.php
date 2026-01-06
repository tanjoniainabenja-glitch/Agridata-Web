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

$email = strtolower(trim($data['email'] ?? ''));
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Email ou mot de passe manquant']);
    exit;
}

try {
    $collection = usersCollection();
    $user = $collection->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode([
            'status' => 'success',
            'user' => [
                'id' => (string) $user['_id'],
                'username' => $user['username'] ?? '',
                'fullname' => $user['fullname'] ?? '',
                'email' => $user['email'],
            ],
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Identifiants incorrects']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur : ' . $e->getMessage()]);
}
