<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Méthode non autorisée.";
    exit();
}

$token = trim($_POST['token'] ?? '');
$newPassword = $_POST['password'] ?? '';

if ($token === '' || $newPassword === '') {
    echo "<script>alert('Token et mot de passe requis.'); window.location.href='index.php';</script>";
    exit();
}

$collection = usersCollection();
$user = $collection->findOne(['reset_token' => $token]);

if (!$user || empty($user['reset_expires']) || $user['reset_expires'] < time()) {
    echo "<script>alert('Token invalide ou expiré.'); window.location.href='index.php';</script>";
    exit();
}

$collection->updateOne(
    ['_id' => $user['_id']],
    [
        '$set' => ['password' => password_hash($newPassword, PASSWORD_DEFAULT)],
        '$unset' => ['reset_token' => '', 'reset_expires' => ''],
    ]
);

echo "<script>alert('Mot de passe mis à jour. Vous pouvez vous connecter.'); window.location.href='index.php';</script>";
exit();
