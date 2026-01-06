<?php
require_once __DIR__ . '/config.php';

$token = $_GET['token'] ?? '';
$token = trim($token);

if ($token === '') {
    echo "<script>alert('Token manquant.'); window.location.href='index.php';</script>";
    exit();
}

$collection = usersCollection();
$user = $collection->findOne(['reset_token' => $token]);

if (!$user || empty($user['reset_expires']) || $user['reset_expires'] < time()) {
    echo "<script>alert('Token invalide ou expiré.'); window.location.href='index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
    <h2>Réinitialiser le mot de passe</h2>
    <form action="reset_process.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="input-box">
            <input type="password" name="password" placeholder="Nouveau mot de passe" required>
        </div>
        <button type="submit" class="btn">Mettre à jour</button>
    </form>
    <div class="sign-link">
        <p><a href="index.php">Retour à la connexion</a></p>
    </div>
</div>
</body>
</html>
