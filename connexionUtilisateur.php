<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';   


// Code de connexion
$db = "bibliocat";
$dbhost = "localhost";
$dbport = 3307;
$dbuser = "compteAdmin";
$dbpasswd = "joliverie";

try {
    $connexion = new PDO('mysql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $db . '', $dbuser, $dbpasswd);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connexion->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $mail = isset($_POST['mail']) ? $_POST['mail'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Vérification des champs de formulaire
    if (empty($mail) || empty($password)) {
        $_SESSION['message'] = "Veuillez remplir tous les champs.";
        header("Location: connexionUtilisateur.php");
        exit();
    }

    try {
        $query = $connexion->prepare("SELECT num_adherent, user_login, user_mdp FROM adherent WHERE mail = :mail");
        $query->bindParam(':mail', $mail, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Hacher le mot de passe saisi par l'utilisateur pour comparaison
            $hashed_password = hash('sha256', $password); // Utilisation de SHA-256 pour hacher le mot de passe

            // Comparer le mot de passe haché saisi par l'utilisateur avec le mot de passe haché stocké dans la base de données
            if ($hashed_password === $user['user_mdp']) {
                $_SESSION['user_id'] = $user['num_adherent'];
                $_SESSION['user_username'] = $user['user_login'];
                $_SESSION['message'] = "Connexion réussie. Bienvenue, " . $user['user_login'] . "!";
                header("Location: reservation.php"); // Rediriger vers une page appropriée après connexion
                exit();
            } else {
                $_SESSION['message'] = "Identifiants incorrects. Veuillez réessayer.";
                header("Location: connexionUtilisateur.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Utilisateur non trouvé. Veuillez vous inscrire.";
            header("Location: inscription.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>BiblioCAT - Gestion des livres</title>
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
            <a href="connexionUtilisateur.php" class="chat-link">
                <button class="menuBloc">Se connecter</button>
                <img src="Images/mochi-peach.gif" alt="chat" id="chat">
            </a>
            <!-- Afficher le bouton "Back Office" uniquement si l'utilisateur est connecté en tant que "compteAdmin" -->
            <?php if ($isCompteAdmin) : ?>
                <a href="back-office.php" ><button class="menuBloc">Back Office</button></a>
            <?php endif; ?>
        </div>
    </header>
    
    <header>
        <h2>Connexion</h2>
    </header>    

    <div class="login-form">
        <h2>Connectez-vous</h2>
        <!-- Affichage des messages de session -->
        <?php if (isset($_SESSION['message'])) : ?>
            <div class="message">
                
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        <form action="connexionUtilisateur.php" method="post">
            <label for="mail">Adresse e-mail :</label>
            <input type="email" name="mail" required>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Se connecter</button>
        </form>

        <p>Vous n'avez pas de compte ? <a href="inscription.php">Inscrivez-vous ici</a>.</p>
    </div>
    
    
    <!-- FOOTER -->
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
