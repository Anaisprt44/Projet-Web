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

$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';
$message = "";

// Vérification si le formulaire de gestion de catégorie est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si le bouton "Ajouter" est cliqué
    if (isset($_POST['add_category'])) {
        // Récupérer la nouvelle catégorie depuis le formulaire
        $new_category = $_POST['new_category'];

        // Vérifier si la catégorie existe déjà dans la base de données
        $stmt_check = $connexion->prepare("SELECT * FROM categorie WHERE libelle = ?");
        $stmt_check->execute([$new_category]);
        $existing_category = $stmt_check->fetch();

        // Si la catégorie n'existe pas déjà, l'ajouter dans la base de données
        if (!$existing_category) {
            $stmt_insert_cat = $connexion->prepare("INSERT INTO categorie (libelle) VALUES (?)");
            $stmt_insert_cat->execute([$new_category]);

            // Récupérer l'ID de la nouvelle catégorie ajoutée
            $new_category_id = $connexion->lastInsertId();

            $message = "La catégorie a été ajoutée avec succès.";
        } else {
            $message = "La catégorie existe déjà dans la base de données.";
        }
    }

    // Si l'identifiant de catégorie est fourni et valide
    if (isset($_POST['category_id']) && isset($_POST['edited_category'])) {
        $category_id = $_POST['category_id'];
        $edited_category = $_POST['edited_category'];

        // Récupérer la catégorie actuelle depuis la base de données
        $stmt_get_category = $connexion->prepare("SELECT libelle FROM categorie WHERE id = ?");
        $stmt_get_category->execute([$category_id]);
        $current_category = $stmt_get_category->fetchColumn();

        // Vérifier si la catégorie a été modifiée
        if ($edited_category !== $current_category) {
            // Modifier la catégorie dans la base de données
            $stmt_edit_cat = $connexion->prepare("UPDATE categorie SET libelle = ? WHERE id = ?");
            $stmt_edit_cat->execute([$edited_category, $category_id]);

            // Vérifier si la modification a été effectuée avec succès
            if ($stmt_edit_cat->rowCount() > 0) {
                $message = "La catégorie a été modifiée avec succès.";
            } else {
                $message = "La modification de la catégorie a échoué.";
            }
        } else {
            $message = "Aucun changement détecté.";
        }
    }

    // Si le bouton "Supprimer" est cliqué
if (isset($_POST['delete_category'])) {
    // Récupérer l'identifiant de la catégorie à supprimer depuis le formulaire
    $category_id = $_POST['category_id'];

    // Vérifier si des livres sont associés à cette catégorie
    $stmt_check_books = $connexion->prepare("SELECT COUNT(*) FROM livre WHERE id_cat = ?");
    $stmt_check_books->execute([$category_id]);
    $num_books = $stmt_check_books->fetchColumn();

    // Si des livres sont associés à cette catégorie, empêcher la suppression
    if ($num_books > 0) {
        $message = "Impossible de supprimer la catégorie car des livres y sont associés.";
    } else {
        // Supprimer la catégorie de la base de données
        $stmt_delete_cat = $connexion->prepare("DELETE FROM categorie WHERE id = ?");
        $stmt_delete_cat->execute([$category_id]);

        // Vérifier si la suppression a été effectuée avec succès
        if ($stmt_delete_cat->rowCount() > 0) {
            $message = "La catégorie a été supprimée avec succès.";
        } else {
            $message = "La suppression de la catégorie a échoué.";
        }
    }
}




}

// Récupération des catégories depuis la base de données
$stmt = $connexion->query("SELECT id, libelle FROM categorie ORDER BY libelle ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Gestion des Catégories</title>
</head>
<header>
        <div class="menu">
            <a href="index.php">
                <img src="Images/logo.png" alt="logo" id="logo">
            </a>
            <a href="index.php">
                <button class="menuBloc">Accueil   </button>
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
            <?php if ($isCompteAdmin) : ?>
                <a href="back-office.php" class="chat-link"><button class="menuBloc">Back Office</button><img src="Images/mochi-peach.gif" alt="chat" id="chat"></a>
                <a href="back-office.php">
                    <form method="POST">
                    <button type="submit" name="destroy_session">Supprimer Session</button>
                </form>

            <?php endif; ?>
        </div>
    </header>
<body>
    <div class="gestionCategorie">
    <h2>Gestion des Catégories :</h2>
    <?php if (!empty($message)) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="new_category">Nouvelle Catégorie :</label>
        <input type="text" id="new_category" name="new_category" required>
        <button type="submit" name="add_category">Ajouter</button>
    </form>
    <h2>Liste des Catégories :</h2>
    <?php foreach ($categories as $category) : ?>
        <div>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                <input type="text" name="edited_category" value="<?php echo $category['libelle']; ?>">
                <button type="submit" name="edit_category">Modifier</button>
            </form>


            <form method="POST" style="display: inline;">
                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                <button type="submit" name="delete_category">Supprimer</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
     <a href="back-office.php"><button class="menuBloc">Retour au Back Office</button></a>
    
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
