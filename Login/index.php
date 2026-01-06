<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">
  <h2 class="text-right">Bienvenue <br><br>
      <span class="highlighted"><img src="../logo.png" alt="logo" class="icon" id="showLogo"></span><br>
      <span class="highlighted"> MINISTÈRE DE  L'AGRICULTURE <br>
        ET DE  L'ÉLEVAGE </span> </h2>
  <form action="login.php" method="POST">
    <h2>Connectez-vous</h2>
    <div class="input-box">
      <input type="email" name="email" placeholder="Email" required>
      <i class='bx bxs-envelope'></i>
    </div>
    <div class="input-box">
      <input type="password" id="password" name="password" placeholder="Mot de passe" required>
      <img src="oeil.png" alt="Afficher le mot de passe" width="37" height="28" class="eye-icon" id="showPassword">
    </div>
    <script src="sc.js"></script>
    <div class="forgot-pass"> <a href="forgot.php">Mot de passe oublié ?</a> </div>
    <button type="submit" class="btn">Se connecter</button>
    <div class="sign-link">
      <p>Vous n'avez pas de compte ? <a href="reg.php">Créer un compte</a></p>
    </div>
  </form>
</div>
</body>
</html>
