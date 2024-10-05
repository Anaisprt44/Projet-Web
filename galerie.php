<?php
session_start(); // Démarrer la session

// Définir les informations de connexion à la base de données en tant que constantes
define('DB_HOST', 'localhost');
define('DB_PORT', 3307);
define('DB_NAME', 'bibliocat');
define('DB_USER', 'compteAdmin');
define('DB_PASSWORD', 'joliverie');

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';

try {
    // connexion à la base de données
    $connexion = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connexion->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    exit();
}

// Définir la variable $boutonAdmin avec une valeur par défaut
$boutonAdmin = '';

// Vérifie si l'utilisateur est connecté et s'il est administrateur
if(isset($_SESSION['user_username']) && $_SESSION['user_username'] == 'compteAdmin') {
    $boutonAdmin = '<a href="gestionLivres.php"><button class="button-traitement ">Gestion des Livres</button></a>';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
        $search = $_POST['search'];

        // Requête SQL avec utilisation de paramètres préparés pour éviter les injections SQL
        $requete = "SELECT * FROM livre l WHERE titre LIKE :search";

        // Préparez la requête SQL
        $stmt = $connexion->prepare($requete);

        // Bind des valeurs des paramètres
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);

        // Exécutez la requête SQL
        $stmt->execute();
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
    <title>BiblioCAT</title>
</head>
<body>
    <header>
             
       <div class="menu">                    
            <a href="index.php">
                <img src="Images/logo.png" alt="logo" id="logo">
            </a>
            <a href="index.php">
                <button class="menuBloc">Accueil</button> 
            </a>

            <a href="reservation.php" onclick="<?php echo (isset($_SESSION['user_id']) ? '' : 'alert(\'Vous devez vous connecter avant de réserver\'); return false;'); ?>" class="chat-link">
                <button class="menuBloc">Réservation</button>
            </a>

            <a href="galerie.php" class="chat-link">
                <button class="menuBloc">Galerie</button>
                <img src="Images/mochi-peach.gif" alt="chat" id="chat">
            </a>
            <a href="contact.php">
                <button class="menuBloc">Contact</button>
            </a>
            <a href="connexionUtilisateur.php">
                <button class="menuBloc">Se connecter</button>
            </a>
           <!-- Afficher le bouton "Back Office" uniquement si l'utilisateur est connecté en tant que "compteAdmin" -->
            <?php if ($isCompteAdmin) : ?>
                <a href="back-office.php"><button class="menuBloc">Back Office</button></a>
            <?php endif; ?>
        </div>
         
    </header>   

    <main>
       
        <div class="galerie">
            <h2>Notre galerie</h2>
            <br>           
               
            <!-- Affichage du bouton administrateur -->
            <?php echo $boutonAdmin; ?>

            <form method="post" action="galerie.php" style="margin-top: 20px;">                    
                <div class="search-container">
                    <input class="text-recherche" type="text" placeholder="Rechercher un livre" name="search">                      
                </div>
            </form>


            <div class="boutique">               
                <?php
                    if(isset($stmt)) {
                        while ($couverture = $stmt->fetch()) {
                            echo '<div class="grid-item">
                                      <a href="galerie-infos.php?id=' . $couverture["ref"] . '">
                                          <img src="Images/'.$couverture["photo"].'" height="200px" class="zoom">
                                      </a>
                                  </div>';
                        }
                    } else {
                        $listeCouverture = $connexion->query("SELECT * FROM livre ORDER BY titre ASC");

                        while ($couverture = $listeCouverture->fetch()) {
                            echo '<div class="grid-item">
                                      <a href="galerie-infos.php?id=' . $couverture["ref"] . '">
                                          <img src="Images/'.$couverture["photo"].'" height="200px" class="zoom">
                                      </a>
                                  </div>';
                        }
                    }
                ?>            
            </div>
        </div>        
    </main>
    
    <!-- flèche retour haut -->
    <a href="#" class="retour-haut">
        <span class="material-symbols-outlined">arrow_upward_alt</span>
    </a>
    
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
