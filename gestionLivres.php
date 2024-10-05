<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';
try {
        
    // Connexion à la base de données
    $db = "bibliocat";
    $dbhost = "localhost";
    $dbport = 3307;
    $dbuser = "compteAdmin";
    $dbpasswd = "joliverie";

    $connexion = new PDO('mysql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $db, $dbuser, $dbpasswd);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connexion->exec("SET CHARACTER SET utf8");
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    exit();
}

// Vérifie si l'utilisateur est connecté et s'il est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'compteAdmin') {
    $_SESSION['message'] = "Accès refusé. Seul l'administrateur peut accéder à cette page.";
}

// Traitement de la soumission du formulaire pour supprimer un livre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_book'])) {
    $book_id = htmlspecialchars($_POST['delete_book']);

    // Supprimer le livre de la base de données
    $stmt = $connexion->prepare("DELETE FROM livre WHERE ref = ?");
    $stmt->execute([$book_id]);

    // Vérifier les erreurs après l'exécution de la requête
    if ($stmt->errorCode() != '00000') {
        echo "Erreur lors de la suppression du livre: ";
        print_r($stmt->errorInfo());
    } else {
        ?>
        <div class="message-traitement">         
            <?php
            echo "Livre supprimé avec succès.";
            ?>
        </div>
        <?php
    }
}
// Traitement de la soumission du formulaire pour ajouter un livre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    // Vérifier si une nouvelle langue est ajoutée
    if ($_POST['id_langue'] === 'new') {
        // Récupérer la nouvelle langue
        $new_langue = htmlspecialchars($_POST['new_langue']);

        // Vérifier si la langue existe déjà
        $stmt_check_langue = $connexion->prepare("SELECT DISTINCT langue FROM livre WHERE langue = ?");
        $stmt_check_langue->execute([$new_langue]);
        $existing_langue = $stmt_check_langue->fetchColumn();

        if ($existing_langue) {
            // Utiliser la langue existante
            $langue = $existing_langue;
        } else {
            // Ajouter la nouvelle langue
            $stmt_insert_langue = $connexion->prepare("INSERT INTO livre (langue) VALUES (?)");
            $stmt_insert_langue->execute([$new_langue]);
            $langue = $new_langue;
        }
    } else {
        // Utiliser une langue existante sélectionnée par l'utilisateur
        $langue = htmlspecialchars($_POST['id_langue']);
    }

    // Maintenant vous pouvez utiliser $langue pour insérer le livre dans la base de données
}

// Traitement de la soumission du formulaire pour ajouter un livre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    if (isset($_FILES['new_photo']) && $_FILES['new_photo']['error'] === UPLOAD_ERR_OK) {
        // Récupérer les autres données du formulaire
        $new_titre = htmlspecialchars($_POST['new_titre']);
        $new_auteur = htmlspecialchars($_POST['new_auteur']);
        $new_langue = htmlspecialchars($_POST['new_langue']);
        $new_annee = htmlspecialchars($_POST['new_annee']);
        $new_descrip = htmlspecialchars($_POST['new_descrip']);
        $new_iban = htmlspecialchars($_POST['new_iban']);

        // Récupérer l'ID de la catégorie
        $id_cat = null;
        if ($_POST['id_cat'] !== 'new') {
            $id_cat = htmlspecialchars($_POST['id_cat']);
        } elseif (isset($_POST['new_cat'])) {
            $new_cat = htmlspecialchars($_POST['new_cat']);

            // Vérifier si la catégorie existe déjà
            $stmt_check_cat = $connexion->prepare("SELECT id FROM categorie WHERE libelle = ?");
            $stmt_check_cat->execute([$new_cat]);
            $existing_cat = $stmt_check_cat->fetchColumn();

            if ($existing_cat) {
                $id_cat = $existing_cat;
            } else {
                // Insérer la nouvelle catégorie
                $stmt_insert_cat = $connexion->prepare("INSERT INTO categorie (libelle) VALUES (?)");
                $stmt_insert_cat->execute([$new_cat]);
                $id_cat = $connexion->lastInsertId();
            }
        }

        // Télécharger la photo
        $new_photo = htmlspecialchars($_FILES['new_photo']['name']);
        $target_dir = "Images/";
        $target_file = $target_dir . basename($_FILES["new_photo"]["name"]);
        move_uploaded_file($_FILES["new_photo"]["tmp_name"], $target_file);

        // Insérer le nouveau livre dans la base de données
        $stmt_add_book = $connexion->prepare("INSERT INTO livre (titre, auteur, langue, annee, descrip, iban, photo, id_cat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_add_book->execute([$new_titre, $new_auteur, $new_langue, $new_annee, $new_descrip, $new_iban, $new_photo, $id_cat]);

        // Vérifier les erreurs après l'exécution de la requête
        if ($stmt_add_book->errorCode() != '00000') {
            echo "Erreur lors de l'ajout du livre: ";
            print_r($stmt_add_book->errorInfo());
        } else {
            echo "<div class=\"message-traitement\">Livre ajouté avec succès.</div>";
        }
    } else {
        echo "<div class=\"message-traitement\">Aucun fichier n'a été téléchargé ou une erreur s'est produite lors du téléchargement du fichier. ";
        echo "Code d'erreur lors du téléchargement du fichier : " . $_FILES['new_photo']['error'] . "</div>";
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
                <button class="menuBloc">Accueil</button> 
            </a>
            <a href="reservation.php">
                    <button class="menuBloc" onclick="<?php echo isset($_SESSION['user_id']) ? '' : 'alert(\'Vous devez vous connecter avant de réserver\')'; ?>">Réservation</button>
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
    <h1>Gestion des livres</h1>
    <main>
        <div class="gestionLivre-centre">
            <div class="galerie-gestionLivres">


                <!-- Formulaire d'ajout de livre -->
                <h2>Ajouter un nouveau livre</h2>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

                    <input type="text" id="new_titre" name="new_titre"  placeholder="Titre" required><br>

                    <input type="text" id="new_auteur" name="new_auteur" placeholder="Auteur" required><br>

                    <!-- Choisir une langue existante -->                    
                    <select id="id_langue" name="id_langue" onchange="toggleNewLangue()">
                        <option value="" disabled selected>Sélectionner une langue existante</option>
                        <?php
                        // Récupérer les langues depuis la base de données
                        $stmt_langue = $connexion->prepare("SELECT DISTINCT langue FROM livre");
                        $stmt_langue->execute();
                        $langues = $stmt_langue->fetchAll();

                        // Afficher chaque langue dans la liste déroulante
                        foreach ($langues as $langue) {
                            $langue_name = $langue['langue'];
                            echo "<option value='$langue_name'>$langue_name</option>";
                        }
                        ?>
                        <option value="new">Ajouter une nouvelle langue</option>
                    </select>
                    <br>

                    <!-- Champ texte pour ajouter une nouvelle langue -->
                    <div id="newLangueField" style="display: none;">
                        
                        <input type="text" id="new_langue" name="new_langue" placeholder="Nouvelle Langue">
                        
                    </div>

                    <script>
                        function toggleNewLangue() {
                            var select = document.getElementById("id_langue");
                            var newLangueField = document.getElementById("newLangueField");
                            var selectedValue = select.options[select.selectedIndex].value;
                            if (selectedValue === "new") {
                                newLangueField.style.display = "block";
                            } else {
                                newLangueField.style.display = "none";
                            }
                        }
                    </script>
                    <br>
                    
                    <input type="number" id="new_annee" name="new_annee" placeholder="Année" required><br>

                     <!-- Choisir une catégorie existante -->
                    
                    <select id="id_cat" name="id_cat" onchange="toggleNewCategory()">
                        <option value="" disabled selected>Sélectionner une catégorie existante</option>
                        <?php
                        // Récupérer les catégories depuis la base de données
                        $stmt_cat = $connexion->prepare("SELECT id, libelle FROM categorie");
                        $stmt_cat->execute();
                        $categories = $stmt_cat->fetchAll();

                        // Afficher chaque catégorie dans la liste déroulante
                        foreach ($categories as $categorie) {
                            $id_categorie = $categorie['id'];
                            $libelle_categorie = $categorie['libelle'];
                            echo "<option value='$id_categorie'>$libelle_categorie</option>";
                        }
                        ?>
                        <option value="new">Ajouter une nouvelle catégorie</option>
                    </select>
                    <br>

                    <!-- Champ texte pour ajouter une nouvelle catégorie -->
                    <div id="newCategoryField" style="display: none;">
                        
                        <input type="text" id="new_cat" name="new_cat" placeholder="Nouvelle Catégorie">
                        <br>
                    </div>

                    <script>
                        function toggleNewCategory() {
                            var select = document.getElementById("id_cat");
                            var newCategoryField = document.getElementById("newCategoryField");
                            var selectedValue = select.options[select.selectedIndex].value;
                            if (selectedValue === "new") {
                                newCategoryField.style.display = "block";
                            } else {
                                newCategoryField.style.display = "none";
                            }
                        }
                    </script>

                    <label for="new_photo">Photo:</label><br>
                    <input type="file" id="new_photo" name="new_photo" ><br>


                    <textarea id="new_descrip" name="new_descrip" placeholder="Description" required></textarea><br>


                    <input type="text" id="new_iban" name="new_iban" placeholder="ISBN" pattern=".{6}" title="Please entrez 6 charactères " required><br> 

                    <input type="submit" name="add_book" value="Ajouter">
                </form>
            </div>

        </div>

        <hr>

         <!-- Liste des livres -->        
        <div>
            <h2>Liste des livres</h2>  
            <div class="boutique">

                <?php
                    $listeCouverture = $connexion->query("SELECT livre.*, categorie.libelle AS categorie_nom FROM livre LEFT JOIN categorie ON livre.id_cat = categorie.id ORDER BY titre ASC");
                    while ($couverture = $listeCouverture->fetch()) {
                        echo '<div class ="grid-item">';
                        echo '<a href="galerie-infos.php?id=' . $couverture["ref"] . '">';
                        echo '<img src="Images/'.$couverture["photo"].'" height="200px" class="zoom">';
                        echo '</a>';
                        
                        echo '<form action="gestionLivres-modification.php" method="post">';
                        echo '<input type="hidden" name="ref" value="' . $couverture["ref"] . '">';
                        echo '<input type="submit" value="Modifier le livre">';
                        echo '</form>';
                        echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
                        echo '<input type="hidden" name="delete_book" value="' . $couverture["ref"] . '">';
                        echo '<input type="submit" value="Supprimer">';
                        echo '</form>';
                        echo '</div>';
                    }
                ?>            
            </div> 
            <div>
                <a href="back-office.php"><button class="menuBloc">Retour au Back Office</button></a>
            </div>
        </div>
    </main>
    

    <a href="#" class="retour-haut">
        <span class="material-symbols-outlined">arrow_upward_alt</span>
    </a>

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