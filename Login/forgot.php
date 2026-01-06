<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
    <h2>Mot de passe oublié</h2>
    <p>Entrez votre email pour recevoir un lien de réinitialisation.</p>
    <form action="forgot_process.php" method="POST">
        <div class="input-box">
            <input type="email" name="email" placeholder="Adresse email" required>
        </div>
        <button type="submit" class="btn">Envoyer</button>
    </form>
    <div class="sign-link">
        <p><a href="index.php">Retour à la connexion</a></p>
    </div>
</div>
</body>
</html>
