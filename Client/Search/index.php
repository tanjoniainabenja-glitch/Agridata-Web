<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriData Madagascar - Données Agricoles et d'Élevage</title>
    
    <!-- Polices & Icônes -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="style.css">
</head>
<body>
	
	
    <header class="site-header fade-in-up">
        <div class="container">
			<img src="logo.png" alt="Logo de chargement AgriData Mada" class="logo"> 
            <a href="/" class="logo">
                <i class="fa-solid fa-leaf"></i>
                <strong>AgriData</strong>
                <span>Madagascar</span>
            </a>
            <nav class="main-nav">
                <a href="../../index.html">Accueil</a>
                <a href="../Search/index.html">Données</a>
                <a href="../Carte/index.html">Cartes</a>
                <a href="../Api/index.html">API</a>
                <a href="../Contact/index.html">Contact</a>
            </nav>
            <div class="header-actions">
                <form class="search-form" action="../Search/index.html">
                    <input type="search" placeholder="Rechercher une donnée">
                    <button type="submit" aria-label="Rechercher"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <a href="#" class="btn btn-login"><i class="fa-solid fa-user"></i> Connexion</a>
            </div>
        </div>
    </header>
	
	 <!-- 2. INCLUSION DE LA SECTION DES PRODUITS ICI -->
        <?php include 'produit.php'; ?>
        
        <!-- Vous pouvez ajouter d'autres contenus de la page d'accueil ici si nécessaire -->

    <footer class="site-footer fade-in-up">
        <div class="container">
            <nav class="footer-links">
                <a href="../Apropos/index.html">À propos</a>
                <a href="../Source/index.html">Sources des données</a>
                <a href="../Contact/index.html">Contact</a>
                <a href="../Droit/index.html">Mentions Légales</a>
            </nav>
            <p>&copy; 2025 AgriData Mada. Tous droits réservés.</p>
        </div>
    </footer>
	<script src="script.js"></script>
</body>
</html>