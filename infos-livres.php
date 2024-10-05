<?php
    session_start(); // Démarrer la session

    // Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
    $isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';

        
// Connexion à la base de données
$db = "bibliocat";
$dbhost = "localhost";
$dbport = 3307;
$dbuser = "compteAdmin";
$dbpasswd = "joliverie";
$connexion = new PDO('mysql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $db . '', $dbuser, $dbpasswd);
$connexion->exec("SET CHARACTER SET utf8");
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
            <img src="Images/logo.png" alt="logo">
        </a>
        <a href="index.php"><button class="menuBloc">Accueil</button></a>
        <a href="reservation.php" onclick="<?php echo isset($_SESSION['user_id']) ? '' : 'alert(\'Vous devez vous connecter avant de réserver\')'; ?>" class="chat-link">
                <button class="menuBloc">Réservation</button>
                <img src="Images/mochi-peach.gif" alt="chat" id="chat">                            
        </a>
        <a href="galerie.php"><button class="menuBloc">Galerie</button></a>
        <a href="contact.php"><button class="menuBloc">Contact</button></a>
        <a href="connexionUtilisateur.php"><button class="menuBloc">Se connecter</button>  </a>
    </div>
</header>
    <div id="bloc-info-livre">
        <div class="infos-livre">
            <?php
            // Récupération de la référence du livre depuis l'URL
            $ref = isset($_GET["ref"]) ? $_GET["ref"] : '';

            // Vérification de la référence du livre
            if (!empty($ref)) {
                // Requête pour récupérer les informations sur le livre
                $stmt_livre = $connexion->prepare("SELECT titre, photo FROM livre WHERE ref=:ref");
                $stmt_livre->bindValue(':ref', $ref, PDO::PARAM_STR);
                $stmt_livre->execute();
                $livre_info = $stmt_livre->fetch(PDO::FETCH_ASSOC);

                // Vérification si le livre existe
                if ($livre_info) {
                    // Affichage du titre du livre
                    echo '<p id="titre-infos-livre">Informations pour le livre : ' . $livre_info["titre"] . '</p>';
                    // Affichage de l'image du livre
                    echo '<div class="flex-item-right image-container">';
                    echo '<img src="Images/' . $livre_info["photo"] . '" alt="Image du livre">';
                    echo '</div>';
                    // Requête pour récupérer les emprunts pour ce livre
                    $stmt_emprunt = $connexion->prepare("SELECT e.date_deb, e.date_fin, a.num_adherent
                    FROM emprunt e
                    INNER JOIN adherent a ON e.num_adherent = a.num_adherent
                    WHERE e.ref=:ref");
                    $stmt_emprunt->bindValue(':ref', $ref, PDO::PARAM_INT);
                    $stmt_emprunt->execute();
                    $result = $stmt_emprunt->fetchAll(PDO::FETCH_ASSOC);

                    // Affichage des emprunts pour ce livre
                    if (count($result) > 0) {
                        echo '<div class="emprunt-container">';
                        foreach ($result as $row) {
                            echo '<div class="emprunt-item">';
                            echo '<p>Date de début : ' . $row["date_deb"] . '</p>';
                            echo '<p>Date de fin : ' . $row["date_fin"] . '</p>';
                            echo '<p>Numéro de l\'adhérent ayant emprunté : ' . $row["num_adherent"] . '</p>';
                            echo '<hr></div>';
                        }
                        echo '</div>'; // Fermeture de emprunt-container
                    } else {
                        echo '<p>Aucun emprunt pour ce livre.</p>';
                    }

                    
                } else {
                    // Le livre n'existe pas
                    echo '<p>Le livre demandé n\'existe pas.</p>';
                }
            } else {
                // La référence du livre n'a pas été fournie
                echo '<p>La référence du livre n\'a pas été fournie.</p>';
            }
            ?>
        </div>
    </div>


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