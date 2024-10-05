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
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activer les erreurs PDO
$connexion->exec("SET CHARACTER SET utf8");

// Vérifier si la variable de session user_id est définie
if (!isset($_SESSION['user_id'])) {
    // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header("Location: connexionUtilisateur.php");
    exit(); // Assurez-vous de terminer le script après la redirection
}

// récupération variables de session
$user_id = $_SESSION['user_id'];
$user_username = $_SESSION['user_username'];

// Initialisez la variable $stmt à null
$stmt = null;

// Effectuez la requête SQL en fonction des données du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
        $search = $_POST['search'];

        // Requête SQL avec utilisation de paramètres préparés pour éviter les injections SQL
        $requete = "SELECT *, IF(EXISTS(SELECT 1 FROM emprunt WHERE ref = l.ref), 'emprunte', 'disponible') AS disponibilite FROM livre l WHERE titre LIKE :search";

        // Préparez la requête SQL
        $stmt = $connexion->prepare($requete);

        // Bind des valeurs des paramètres
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);

        // Exécutez la requête SQL
        $stmt->execute();

    } elseif (isset($_POST['categorie'])) {
        $categorie = $_POST['categorie'];

        // Requête SQL avec utilisation de paramètres préparés pour éviter les injections SQL
        $requete = "SELECT *, IF(EXISTS(SELECT 1 FROM emprunt WHERE ref = l.ref), 'emprunte', 'disponible') AS disponibilite FROM livre l WHERE l.id_cat = :categorie";

        // Préparez la requête SQL
        $stmt = $connexion->prepare($requete);

        // Bind des valeurs des paramètres
        $stmt->bindValue(':categorie', $categorie, PDO::PARAM_INT);

        // Exécutez la requête SQL
        $stmt->execute();
    }
}

// Traitement pour la réservation des livres
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reserver'])) {
    // Vérifiez si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        // Récupérez les données du formulaire
        $ref_livre = $_POST['reserver'];

        // Calcul de la date de début (aujourd'hui)
        $date_deb = date('Y-m-d');

        // Calcul de la date de fin (15 jours après la date de début)
        $date_fin = date('Y-m-d', strtotime($date_deb . ' + 15 days'));

        // Requête SQL pour insérer une nouvelle réservation dans la table appropriée
        $requete_reservation = "INSERT INTO emprunt (num_adherent, ref, date_deb, date_fin) VALUES (:user_id, :ref_livre, :date_deb, :date_fin)";
        $stmt_reservation = $connexion->prepare($requete_reservation);
        $stmt_reservation->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_reservation->bindValue(':ref_livre', $ref_livre, PDO::PARAM_STR);
        $stmt_reservation->bindValue(':date_deb', $date_deb, PDO::PARAM_STR);
        $stmt_reservation->bindValue(':date_fin', $date_fin, PDO::PARAM_STR);

        // Vérifier si le livre est déjà réservé par l'utilisateur
        $requete_verif_reservation = "SELECT COUNT(*) FROM emprunt WHERE num_adherent = :user_id AND ref = :ref_livre";
        $stmt_verif_reservation = $connexion->prepare($requete_verif_reservation);
        $stmt_verif_reservation->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_verif_reservation->bindValue(':ref_livre', $ref_livre, PDO::PARAM_STR);
        $stmt_verif_reservation->execute();
        $reservation_count = $stmt_verif_reservation->fetchColumn();

        if ($reservation_count > 0) {
            // Le livre est déjà réservé par l'utilisateur
            $_SESSION['error_message'] = "Vous avez déjà réservé ce livre.";
            header("Location: reservation.php");
            exit();
        }

        // Exécutez la requête SQL pour effectuer la réservation
        if ($stmt_reservation->execute()) {
            // La réservation a été effectuée avec succès
            $_SESSION['message'] = "Le livre a été réservé avec succès.";
            header("Location: reservation.php");
            exit();
        } else {
            // Une erreur s'est produite lors de la réservation
            $_SESSION['error_message'] = "Une erreur s'est produite lors de la réservation du livre.";
            header("Location: reservation.php");
            exit();
        }
    } else {
        // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
        $_SESSION['error_message'] = "Vous devez vous connecter avant de réserver un livre.";
        header("Location: connexionUtilisateur.php");
        exit();
    }
}

// Traitement pour la suppression d'un livre réservé par l'utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer_emprunt'])) {
    // Vérifier si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        // Récupérer la référence du livre à supprimer
        $ref_livre = $_POST['supprimer_emprunt'];

        // Requête SQL pour supprimer l'emprunt de la base de données
        $requete_suppression = "DELETE FROM emprunt WHERE ref = :ref_livre AND num_adherent = :user_id";
        $stmt_suppression = $connexion->prepare($requete_suppression);
        $stmt_suppression->bindValue(':ref_livre', $ref_livre, PDO::PARAM_STR);
        $stmt_suppression->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        // Exécuter la requête SQL pour supprimer l'emprunt
        if ($stmt_suppression->execute()) {
            // Emprunt supprimé avec succès
            $_SESSION['message'] = "L'emprunt a été supprimé avec succès.";
        } else {
            // Erreur lors de la suppression de l'emprunt
            $_SESSION['error_message'] = "Une erreur s'est produite lors de la suppression de l'emprunt.";
        }

        // Rediriger l'utilisateur vers la page de réservation
        header("Location: reservation.php");
        exit(); // Assurez-vous de terminer le script après la redirection
    } else {
        // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
        $_SESSION['error_message'] = "Vous devez vous connecter avant de supprimer un livre réservé.";
        header("Location: connexionUtilisateur.php");
        exit();
    }
}
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
                <button class="menuBloc">Accueil   </button>
            </a>
            <a href="reservation.php" class="chat-link">
                <button class="menuBloc">Réservation</button>
                <img src="Images/mochi-peach.gif" alt="chat" id="chat">
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
                <a href="back-office.php"><button class="menuBloc">Back Office</button></a>
            <?php endif; ?>
        </div>
    </header>

    <?php if (isset($_SESSION['error_message'])) : ?>
        <!-- Affichage du message d'erreur -->
        <div class="error-message">
            <?php echo $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <div class="bloc-reservation">

        <div>
            
            
            <div class="bloc-reservation-recherche">
                <form method="post" action="reservation.php">
                    <div>
                        <select id="bouton-filtre" name="categorie">
                            <option value="test" disabled selected>Categorie</option>
                            <?php
                            $listeCate = $connexion->query("SELECT * FROM categorie ORDER BY libelle");

                            while ($cate = $listeCate->fetch()) {
                                echo '<option value="' . $cate["id"] . '">' . $cate["libelle"] . '</option>';
                            }
                            ?>
                        </select>
                        <button type="submit" id="afficherLivre">Afficher les livres par catégorie</button>
                    </div>
                </form>
                <form method="post" action="reservation.php">
                    <div class="search-container">
                        <input class="text-recherche" type="text" placeholder="Rechercher un livre" name="search">
                        <br>
                        <button type="submit" id="afficherLivre"">Rechercher par nom</button>
                    </div>
                </form>

                <div class="bloc-reservation-recherche-grid">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") : ?>
                        <?php if ($stmt !== null && $stmt->rowCount() > 0) : ?>
                            <?php while ($livre = $stmt->fetch()) : ?>
                                <div class="grid-item">
                                    <img src="Images/<?php echo $livre["photo"]; ?>" height="200px" alt="<?php echo $livre["titre"]; ?>">
                                    <div class="livre-details">
                                        <p>IBAN: <?php echo $livre["iban"]; ?></p>
                                        <p>Titre: <?php echo $livre["titre"]; ?></p>
                                        <p>Auteur: <?php echo $livre["auteur"]; ?></p>
                                        <p>Disponibilité: <?php echo $livre["disponibilite"]; ?></p>
                                        <form method="post" action="">
                                            <input type="hidden" name="ref_livre" value="<?php echo $livre["ref"]; ?>">
                                            <button class="button-traitement" name="reserver" value="<?php echo $livre["ref"]; ?>">Réserver</button>

                                        </form>
                                        <a href="infos-livres.php?ref=<?php echo $livre["ref"]; ?>">Historique d'emprunt</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php elseif ($stmt !== null && $stmt->rowCount() == 0) : ?>
                            <p>Aucun livre trouvé.</p>
                        <?php elseif (!isset($_POST['categorie'])) : ?>
                            <p>Aucune catégorie n'a été sélectionnée.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <a href="mesEmprunts.php">
                        <button>Voir mes emprunts</button>
                    </a>

        <div class="bloc-reservation-biblio">
            <div id="text-ma-biblio">
                <p>Ma bibliothèque de livres réservés pour <?php echo isset($user_username) ? $user_username : ''; ?>:</p>
            </div>

            <div class="bloc-reservation-biblio-grid">
                <?php
                // Vérifier si la variable de session user_id est définie
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];

                    // Requête SQL pour récupérer les livres réservés par l'utilisateur
                    $requete_reservations = "SELECT l.*, 'emprunte' AS disponibilite FROM livre l INNER JOIN emprunt e ON l.ref = e.ref WHERE e.num_adherent = :user_id";
                    $stmt_reservations = $connexion->prepare($requete_reservations);
                    $stmt_reservations->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt_reservations->execute();


                    // Afficher les livres réservés
                    while ($livre_reserve = $stmt_reservations->fetch()) {
                        echo '<div class="grid-item">
                                <img src="Images/' . $livre_reserve["photo"] . '" height="200px">
                                <div class="livre-details">
                                    <p>' . $livre_reserve["titre"] . '</p>
                                    <p>Auteur: ' . $livre_reserve["auteur"] . '</p>
                                    <form method="post" action="">
                                        <input type="hidden" name="ref_livre" value="' . $livre_reserve["ref"] . '">
                                        <button class="button-traitement" name="supprimer_emprunt" value="' . $livre_reserve["ref"] . '">Supprimer emprunt</button>
                                    </form>
                                </div>
                            </div>';
                    }

                }
                ?>

            </div>

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
