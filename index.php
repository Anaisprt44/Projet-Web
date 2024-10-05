<!DOCTYPE html>
<html lang="fr"> 
    <?php
    session_start(); // Démarrer la session
    // Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
    $isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';
    ?>   
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BiblioCAT</title>
        <link href="styles.css" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    </head>
    <body>

    <div class="wrapper">
        <header>
            <div class="menu">
            <a href="index.php">
                <img src="Images/logo.png" alt="logo" id="logo">                
            </a>
            <a href="index.php" class="chat-link">
                <button class="menuBloc">Accueil</button>
                <img src="Images/mochi-peach.gif" alt="chat" id="chat">
            </a>
            <a href="reservation.php" onclick="<?php echo isset($_SESSION['user_id']) ? '' : 'alert(\'Vous devez vous connecter avant de réserver\')'; ?>" class="chat-link">
                <button class="menuBloc">Réservation</button>
                
            </a>


            <a href="galerie.php">
                <button class="menuBloc">Galerie</button>
            </a>
            <a href="contact.php">
                <button class="menuBloc">Contact</button>
            </a>
            <a href="connexionUtilisateur.php">
                <button class="menuBloc">Se connecter</button>
            </a>
            
            <!-- Afficher le bouton "Back Office" uniquement si l'utilisateur est connecté en tant que "compteAdmin" -->
            <?php if ($isCompteAdmin) : ?>
            <a href="back-office.php" class="chat-link">
                <button class="menuBloc">Back office</button>
                
            </a>
        <?php endif; ?>

        </div>  
        </header>

    <h1>BiblioCAT</h1> 
    <div class="buttonAccueil">
        <a href="galerie.php">
            <button id="button-acceueil">Entrez dans votre BiblioCAT</button>
        </a>
    </div>

    <div id="weather-widget">
        <!-- Ajoutez ici le code du widget Météo France -->
        <iframe id="widget_autocomplete_preview"  width="300" height="150" frameborder="0" src="https://meteofrance.com/widget/prevision/441090"> </iframe>
    </div>

    <!--Footer -->
    <div class="footer-clean">
        <footer>
            <div class="container">
                <div class="item social">
                    <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    <!-- Ajoutez d'autres icônes de réseaux sociaux selon vos besoins -->
                </div>
                <ul>
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Conditions d'utilisation</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
                <div class="copyright">© 2024 BiblioCAT. Tous droits réservés.</div>
            </div>
        </footer>
    </div>
</body>
</html>
