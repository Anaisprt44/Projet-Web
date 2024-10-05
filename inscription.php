<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';
    $user_mdp = isset($_POST['user_mdp']) ? $_POST['user_mdp'] : '';

    if (empty($nom) || empty($prenom) || empty($email) || empty($user_login) || empty($user_mdp)) {
        echo "Tous les champs sont obligatoires.";
        exit();
    }

    $hashed_password = hash('sha256', $user_mdp); // Utilisation de SHA-256 pour hacher le mot de passe
    $date_inscription = date('Y-m-d');
    $sql = "INSERT INTO adherent (nom, prenom, mail, date_inscription, user_login, user_mdp) VALUES (:nom, :prenom, :email, :date_inscription, :user_login, :hashed_password)";

    try {
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':date_inscription', $date_inscription);
        $stmt->bindParam(':user_login', $user_login);
        $stmt->bindParam(':hashed_password', $hashed_password);
        $stmt->execute();
        echo '<p style="color: green; font-weight: bold;">Inscription réussie !</p>';
    } catch (PDOException $e) {
        echo "Erreur lors de l'inscription : " . $e->getMessage();
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
                <img src="Images/logo.png" alt="logo">
            </a>                                                                             
            <a href="index.php"><button class="menuBloc">Accueil</button></a>
            <a href="reservation.php" onclick="<?php echo isset($_SESSION['user_id']) ? '' : 'alert(\'Vous devez vous connecter avant de réserver\')'; ?>" class="chat-link">
                <button class="menuBloc">Réservation</button>

            </a>

            <a href="galerie.php"><button class="menuBloc">Galerie</button></a>
            <a href="contact.php"><button class="menuBloc">Contact</button>  </a>  
            <a href="connexionUtilisateur.php" class="chat-link">
                <button class="menuBloc">Connexion  </button>
                <img src="Images/mochi-peach.gif" alt="chat" id="chat">                            
            </a>
        </div>         
    </header>
    <header>
        <h1>Inscription</h1>
    </header>

    <div class="register-form">
        <h2>Inscrivez-vous</h2>
        <form action="inscription.php" method="post">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" required>

            <label for="email">Adresse e-mail :</label>
            <input type="email" name="email" required>

            <label for="user_login">Nom d'utilisateur :</label>
            <input type="text" name="user_login" required>

            <label for="user_mdp">Mot de passe :</label>
            <input type="password" name="user_mdp" required>

            <button type="submit" name="register">S'inscrire</button>
        </form>

        <p>Vous avez déjà un compte ? <a href="connexionUtilisateur.php">Connectez-vous ici</a>.</p>
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
