<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';

// Vérifier si l'utilisateur est un administrateur, sinon, afficher un message et arrêter le script
if (!$isCompteAdmin) {
    header("Location: index.php");
       
}

// Vérifier si le formulaire de destruction de session est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['destroy_session'])) {
    // Détruire la session
    session_destroy();
    // Rediriger l'utilisateur vers une page d'accueil ou une autre page appropriée après la déconnexion
    header("Location: index.php");
    exit; // Terminer le script pour éviter toute exécution supplémentaire
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Back Office</title>
</head>

<body>
    <header>
        <div class="menu">
            <a href="index.php">
                <img src="Images/logo.png" alt="logo" id="logo">
            </a>
            <a href="index.php">
                <button class="menuBloc">Accueil   </button>
            </a>
            <a href="reservation.php" class="chat-link">
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
                <a href="back-office.php" class="chat-link"><button class="menuBloc">Back Office</button><img src="Images/mochi-peach.gif" alt="chat" id="chat"></a>
                <a href="back-office.php">
                    <form method="POST">
                    <button type="submit" name="destroy_session">Supprimer Session</button>
                </form>

            <?php endif; ?>
        </div>
    </header>
    
    <h1>Back office</h1>
    <!-- Boutons pour différentes fonctionnalités du back office -->
    <a href="gestionLivres.php"><button class="menuBloc">Gestion des Livres</button></a>
    <br>
    <a href="gestionEmprunts.php"><button class="menuBloc">Gestion des Emprunts</button></a>
    <br>
    <a href="gestionUtilisateurs.php"><button class="menuBloc">Gestion des Utilisateurs</button></a>
    <br>
    <a href="gestionCatégorie.php"><button class="menuBloc">Gestion des catégories</button></a>

<div class="footer-clean">
            <footer>
                <div class="container">
                    <div class="item social">
                        <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
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
