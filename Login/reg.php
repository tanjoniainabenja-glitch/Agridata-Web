<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <img src="img.png" alt="logo">
        <h2 class="text-right">Bienvenue</h2>
        
        <form action="register.php" method="POST">
            <h1>Créer un compte</h1>
        
            <div class="input-box">
                <input type="text" name="prenom" placeholder="Prénom" required>
            </div>
        
            <div class="input-box">
                <input type="text" name="nom" placeholder="Nom" required>
            </div>
        
            <div class="input-box">
                <input type="email" name="email" placeholder="Adresse email" required>
            </div>
        
            <div class="input-box">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
        
            <div class="forgot-pass">
                <a href="forgot.php">Mot de passe oublié ?</a>
            </div>
        
            <button type="submit" class="btn">S'inscrire</button>
        
            <div class="sign-link">
                <p>Vous avez déjà un compte ? <a href="index.php">Se connecter</a></p>
            </div>
        </form>

    </div>
</body>
</html>
