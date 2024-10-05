<?php
// Connexion à la base de données
$dbhost = "localhost";
$dbport = 3307;
$dbname = "bibliocat";
$dbuser = "compteAdmin";
$dbpasswd = "joliverie";

session_start(); // Démarrer la session

try {
    $connexion = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $dbuser, $dbpasswd);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$message = "";

// Traitement de la suppression d'emprunt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supprimer_emprunt'])) {
    // Vérifiez si l'utilisateur est connecté
    if (isset($_SESSION['user_id'])) {
        // Récupérer l'identifiant de l'emprunt à supprimer
        $id_emprunt = $_POST['id_emprunt'];

        // Requête SQL pour supprimer l'emprunt de la base de données
        $requete_suppression = "DELETE FROM emprunt WHERE id = :id_emprunt";
        $stmt_suppression = $connexion->prepare($requete_suppression);
        $stmt_suppression->bindValue(':id_emprunt', $id_emprunt, PDO::PARAM_INT);

        // Exécuter la requête SQL pour supprimer l'emprunt
        if ($stmt_suppression->execute()) {
            // Emprunt supprimé avec succès
            $message = "L'emprunt a été supprimé avec succès.";
        } else {
            // Erreur lors de la suppression de l'emprunt
            $message = "Une erreur s'est produite lors de la suppression de l'emprunt.";
        }
    } else {
        // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
        $message = "Vous devez vous connecter avant de supprimer un livre réservé.";
    }
}

// Récupération de l'identifiant de l'utilisateur connecté
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Requête SQL pour récupérer les emprunts de l'utilisateur connecté
    $stmt_emprunts_utilisateur = $connexion->prepare("SELECT * FROM emprunt WHERE num_adherent = :user_id ORDER BY date_fin DESC");
    $stmt_emprunts_utilisateur->bindParam(':user_id', $user_id);
    $stmt_emprunts_utilisateur->execute();
    $emprunts_utilisateur = $stmt_emprunts_utilisateur->fetchAll(PDO::FETCH_ASSOC);
}

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Mes Emprunts</title>
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

     <h2>Mes Emprunts</h2>

    <!-- Affichage des emprunts de l'utilisateur connecté -->
    <?php if (isset($emprunts_utilisateur)) : ?>
        <div class="mesEmpurntsUtil">
            
            <table>
                <thead>
                    <tr>
                        <th>ID Emprunt</th>
                        <th>Référence Livre</th>
                        <th>Nom Livre</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Action</th>
                        <!-- Ajoutez d'autres en-têtes de colonne selon vos besoins -->
                    </tr>
                </thead>
                <tbody>
                    <div class="mesEmpurntsUtil">
                        <?php foreach ($emprunts_utilisateur as $emprunt) : ?>
                            <tr>
                                <td><?php echo $emprunt['id']; ?></td>
                                <td><?php echo $emprunt['ref']; ?></td>
                                <!-- Récupération du titre du livre à partir de la référence -->
                                <?php
                                $stmt_titre_livre = $connexion->prepare("SELECT titre FROM livre WHERE ref = :ref");
                                $stmt_titre_livre->bindParam(':ref', $emprunt['ref']);
                                $stmt_titre_livre->execute();
                                $titre_livre = $stmt_titre_livre->fetchColumn();
                                ?>
                                <td><?php echo $titre_livre; ?></td>
                                <td><?php echo $emprunt['date_deb']; ?></td>
                                <td><?php echo $emprunt['date_fin']; ?></td>
                                <td>
                                    <!-- Bouton pour supprimer l'emprunt -->
                                    <form method="post" action="">
                                        <input type="hidden" name="id_emprunt" value="<?php echo $emprunt['id']; ?>">
                                        <button type="submit" name="supprimer_emprunt">Supprimer emprunt</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </div>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p>Aucun emprunt trouvé pour cet utilisateur.</p>
    <?php endif; ?>
             

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


</html>
