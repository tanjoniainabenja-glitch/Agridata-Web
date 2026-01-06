<?php
require_once __DIR__ . '/config.php';

startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Méthode non autorisée.";
    exit();
}

$prenom = trim($_POST['prenom'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($prenom === '' || $nom === '' || $email === '' || $password === '') {
    echo "<script>alert('Tous les champs sont requis.'); window.location.href='reg.php';</script>";
    exit();
}

$collection = usersCollection();
$existingUser = $collection->findOne(['email' => $email]);

if ($existingUser) {
    echo "<script>alert('Adresse email déjà utilisée.'); window.location.href='reg.php';</script>";
    exit();
}

$fullname = ucfirst(strtolower($prenom)) . ' ' . ucfirst(strtolower($nom));
$username = strtolower($prenom . '.' . $nom);

$insertResult = $collection->insertOne([
    'prenom' => ucfirst(strtolower($prenom)),
    'nom' => ucfirst(strtolower($nom)),
    'fullname' => $fullname,
    'username' => $username,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'created_at' => new MongoDB\BSON\UTCDateTime()
]);

$_SESSION['user'] = [
    '_id' => (string) $insertResult->getInsertedId(),
    'fullname' => $fullname,
    'username' => $username,
    'email' => $email,
];

header('Location: index.php');
exit();
