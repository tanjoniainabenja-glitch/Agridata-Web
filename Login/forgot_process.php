<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Méthode non autorisée.";
    exit();
}

$email = strtolower(trim($_POST['email'] ?? ''));
if ($email === '') {
    echo "<script>alert('Email requis.'); window.location.href='forgot.php';</script>";
    exit();
}

$collection = usersCollection();
$user = $collection->findOne(['email' => $email]);

if (!$user) {
    echo "<script>alert('Aucun compte trouvé pour cet email.'); window.location.href='forgot.php';</script>";
    exit();
}

$token = bin2hex(random_bytes(16));
$expires = (new DateTime('+1 hour'))->getTimestamp();

$collection->updateOne(
    ['_id' => $user['_id']],
    ['$set' => ['reset_token' => $token, 'reset_expires' => $expires]]
);

// Ici vous pouvez envoyer l'email avec $token. En attendant, on redirige avec une alerte.
$resetLink = "reset.php?token={$token}";

echo "<script>alert('Lien de réinitialisation généré. Copiez le token ou contactez l\'administrateur.'); window.location.href='{$resetLink}';</script>";
exit();
