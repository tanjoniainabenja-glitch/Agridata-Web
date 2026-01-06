<?php
require_once __DIR__ . '/config.php';

startSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Méthode non autorisée.";
    exit();
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo "<script>alert('Email et mot de passe sont requis.'); window.location.href='index.php';</script>";
    exit();
}

$collection = usersCollection();
$user = $collection->findOne(['email' => $email]);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
        '_id' => (string) $user['_id'],
        'username' => $user['username'] ?? '',
        'fullname' => $user['fullname'] ?? '',
        'email' => $user['email']
    ];

    header('Location: ../dashboard/dash_user/dashboard.php');
    exit();
}

echo "<script>alert('Identifiants incorrects !'); window.location.href='index.php';</script>";
exit();
