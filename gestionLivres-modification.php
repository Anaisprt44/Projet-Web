<?php

session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';

try {
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

$ref = $titre = $auteur = $langue = $annee = $id_cat = $descrip = '';

// Récupération des données actuelles du livre pour affichage dans le formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ref'])) {
    $ref = $_POST['ref'];

    // Récupérer les données actuelles du livre
    $stmt = $connexion->prepare("SELECT * FROM livre WHERE ref = ?");
    $stmt->execute([$ref]);
    $livre = $stmt->fetch();

    if ($livre) {
        $titre = $livre['titre'];
        $auteur = $livre['auteur'];
        $langue = $livre['langue'];
        $annee = $livre['annee'];
        $id_cat = $livre['id_cat'];
        $descrip = $livre['descrip'];
    } else {
        echo "Livre non trouvé.";
    }
}

// Gestion de la soumission du formulaire pour la mise à jour du livre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
    $ref = $_POST['ref'];

    // Récupérer les valeurs du formulaire
    $titre = $_POST['titre'];
    $auteur = $_POST['auteur'];
    $langue = $_POST['langue'];
    $annee = $_POST['annee'];
    $id_cat = $_POST['id_cat'];
    $descrip = $_POST['descrip'];

    // Si la langue sélectionnée est "autre", récupérer la nouvelle langue
    if ($langue == 'autre') {
        $langue = $_POST['nouvelle_langue'];
    }

    // Si la catégorie sélectionnée est "autre", récupérer la nouvelle catégorie
    if ($id_cat == 'autre') {
        $nouvelle_categorie = $_POST['nouvelle_categorie'];
        
        // Insérer la nouvelle catégorie dans la table "categorie"
        $stmt_insert_cat = $connexion->prepare("INSERT INTO categorie (libelle) VALUES (?)");
        $stmt_insert_cat->execute([$nouvelle_categorie]);
        
        // Récupérer l'ID de la nouvelle catégorie insérée
        $id_cat = $connexion->lastInsertId();
    }

    // Mettre à jour la base de données avec les nouvelles valeurs
    $stmt = $connexion->prepare("UPDATE livre SET titre = ?, auteur = ?, langue = ?, annee = ?, id_cat = ?, descrip = ? WHERE ref = ?");
    $stmt->execute([$titre, $auteur, $langue, $annee, $id_cat, $descrip, $ref]);
    
    // Vérifier les erreurs après l'exécution de la requête
    if ($stmt->errorCode() != '00000') {
        print_r($stmt->errorInfo());
    } else {
        echo "Livre mis à jour avec succès.";
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
    <title>BiblioCAT - Modifier un livre</title>
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
            <a href="contact.html" class="chat-link">
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
        <h1>Modifier un livre</h1>
        <div class="gestion-modification">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="ref" value="<?php echo $ref; ?>">
                <label for="titre">Titre:</label><br>
                <input type="text" id="titre" name="titre" value="<?php echo $titre; ?>"><br>
                <label for="auteur">Auteur:</label><br>
                <input type="text" id="auteur" name="auteur" value="<?php echo $auteur; ?>"><br>
                <label for="langue">Langue:</label><br>
                <select id="langue" name="langue">
                    <?php
                    // Récupérer les langues depuis la base de données
                    $stmt_langue = $connexion->prepare("SELECT DISTINCT langue FROM livre");
                    $stmt_langue->execute();
                    $langues = $stmt_langue->fetchAll();

                    // Afficher chaque langue dans la liste déroulante
                    foreach ($langues as $langue_item) {
                        $langue_nom = $langue_item['langue'];
                        // Vérifie si la langue actuelle du livre correspond à cette langue
                        $selected = ($langue == $langue_nom) ? 'selected' : '';
                        echo "<option value='$langue_nom' $selected>$langue_nom</option>";
                    }
                    ?>
                    <option value="autre">Autre</option>
                </select><br>

                <div id="autreLangue" style="display:none;">
                    <label for="nouvelle_langue">Nouvelle Langue:</label><br>
                    <input type="text" id="nouvelle_langue" name="nouvelle_langue"><br>
                </div>
                    
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var selectLangue = document.getElementById("langue");
                        var autreLangueDiv = document.getElementById("autreLangue");

                        // Écouteur d'événement pour surveiller les changements dans la sélection de langue
                        selectLangue.addEventListener("change", function() {
                            // Si l'option "Autre" est sélectionnée, afficher le champ de saisie de la nouvelle langue
                            if (selectLangue.value === "autre") {
                                autreLangueDiv.style.display = "block";
                            } else {
                                autreLangueDiv.style.display = "none";
                            }
                        });
                    });
                </script>

                <label for="annee">Année:</label><br>
                <input type="number" id="annee" name="annee" value="<?php echo $annee; ?>"><br>
                <label for="id_cat">Catégorie:</label><br>
                <select id="id_cat" name="id_cat">
                    <?php
                    // Récupérer les catégories depuis la base de données
                    $stmt_cat = $connexion->prepare("SELECT id, libelle FROM categorie");
                    $stmt_cat->execute();
                    $categories = $stmt_cat->fetchAll();

                    // Afficher chaque catégorie dans la liste déroulante
                    foreach ($categories as $categorie) {
                        $id_categorie = $categorie['id'];
                        $libelle_categorie = $categorie['libelle'];
                        // Vérifie si la catégorie actuelle du livre correspond à cette catégorie
                        $selected = ($id_cat == $id_categorie) ? 'selected' : '';
                        echo "<option value='$id_categorie' $selected>$libelle_categorie</option>";
                    }
                    ?>
                    <option value="autre">Autre</option>
                </select><br>

                <div id="autreCategorie" style="display:none;">
                    <label for="nouvelle_categorie">Nouvelle Catégorie:</label><br>
                    <input type="text" id="nouvelle_categorie" name="nouvelle_categorie"><br>
                </div>

                <script>
                    document.getElementById("id_cat").addEventListener("change", function() {
                        var autreCategorieDiv = document.getElementById("autreCategorie");
                        if (this.value == "autre") {
                            autreCategorieDiv.style.display = "block";
                        } else {
                            autreCategorieDiv.style.display = "none";
                        }
                    });
                </script>

                <br>
                <label for="descrip">Description:</label><br>
                <textarea id="descrip" name="descrip"><?php echo $descrip; ?></textarea><br>
                <input type="submit" name="update_book" value="Enregistrer">
            </form>

            <form action="gestionLivres.php" method="post">
                <input type="hidden" name="delete_book" value="<?php echo $ref; ?>">
                <input type="submit" value="Supprimer">
            </form>
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
                    <!-- Ajoutez d'autres icônes de réseaux sociaux -->
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
