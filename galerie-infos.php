<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
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
            <a href="reservation.php" onclick="<?php echo isset($_SESSION['user_id']) ? '' : 'alert(\'Vous devez vous connecter avant de réserver\')'; ?>" class="chat-link">
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
    <div class="galerie-infos">
        <div class="bloc-image_galerie">
            <?php

            try {
                $db = "bibliocat";
                $dbhost = "localhost";
                $dbport = 3307;
                $dbuser = "compteAdmin";
                $dbpasswd = "joliverie";

                $connexion = new PDO('mysql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $db, $dbuser, $dbpasswd);
                $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $connexion->exec("SET CHARACTER SET utf8");
                
                $compteAdmin = false; 
                if(isset($_SESSION['email']) && $_SESSION['email'] === "compteAdmin@email.com" && isset($_SESSION['login']) && $_SESSION['login'] === "compteAdmin") {
                    $compteAdmin = true;
                }

                // Vérifier si l'ID du livre est passé dans l'URL
                if (isset($_GET['id'])) {
                    $id_livre = $_GET['id'];

                    // Récupérer les informations du livre depuis la base de données
                    $query = "SELECT * FROM livre WHERE ref = :id";
                    $stmt = $connexion->prepare($query);
                    $stmt->bindParam(':id', $id_livre);
                    $stmt->execute();
                    $livre = $stmt->fetch();

                    // Si le livre existe
                    if ($livre) {
                        // Afficher l'image du livre
                        ?>
                        <div class="galerie-infos-livre">
                           <?php echo '<img src="Images/' . $livre['photo'] . '" height="200px">';?>          
                        </div>            
                            <?php
                                } else {
                        echo "Livre non trouvé.";
                    }
                } else {
                    echo "ID du livre non spécifié.";
                }
            } catch (PDOException $e) {
                echo "Erreur de connexion à la base de données: " . $e->getMessage();
                exit();
            }
            ?>
        </div>

        <div class="bloc-galerie-infos">
            <?php
            // Afficher les autres informations du livre
            if ($livre) {
                echo '<h2><strong>' . " " . $livre['titre'] . '</strong></h2>';
                echo '<p><strong>ISBN:</strong>' . " " . $livre['iban'] . '</p>';
                echo '<p><strong>Auteur:</strong>' . " " .  $livre['auteur'] . '</p>';
                echo '<p><strong>Langue:</strong>' . " " . $livre['langue'] . '</p>';
                echo '<p><strong>Année:</strong>' . " " . $livre['annee'] . '</p>';

                // Récupérer le libellé de la catégorie du livre depuis la table catégorie
                $query_cat = "SELECT libelle FROM categorie WHERE id = :id_cat";
                $stmt_cat = $connexion->prepare($query_cat);
                $stmt_cat->bindParam(':id_cat', $livre['id_cat']);
                $stmt_cat->execute();
                $categorie = $stmt_cat->fetch();

                echo '<p><strong>Catégorie: </strong>' . $categorie['libelle'] . '</p>';
                echo '<p><strong>Description :</strong>' . " " . $livre['descrip'] . '</p>';
            }           
            
            ?>
            
                <a href="reservation.php" class="button">Réserver maintenant</a>

            
        </div>
    </div>
    </div

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
