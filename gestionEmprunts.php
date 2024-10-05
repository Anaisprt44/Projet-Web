<?php
// Connexion à la base de données
$dbhost = "localhost";
$dbport = 3307;
$dbname = "bibliocat";
$dbuser = "compteAdmin";
$dbpasswd = "joliverie";

session_start(); // Démarrer la session

// Initialisation de la variable $isCompteAdmin à false par défaut
$isCompteAdmin = false;

// Vérifier si l'utilisateur est connecté et est un compte admin
if (isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin') {
    $isCompteAdmin = true;
}

try {
    $connexion = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $dbuser, $dbpasswd);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$message = "";

// Vérification si le formulaire de mise à jour est soumis et si le bouton "Mettre à jour" a été cliqué
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Récupérer les nouvelles valeurs soumises
    $id = $_POST['id'];
    $new_date_deb = date('Y-m-d', strtotime($_POST['date_deb']));
    $new_date_fin = date('Y-m-d', strtotime($_POST['date_fin']));
    $new_ref = $_POST['ref'];
    $new_num_adherent = $_POST['num_adherent'];
    // Récupérer les anciennes valeurs de l'emprunt
    $stmt_old_values = $connexion->prepare("SELECT ref, DATE_FORMAT(date_deb, '%Y-%m-%d') as date_deb, DATE_FORMAT(date_fin, '%Y-%m-%d') as date_fin, num_adherent FROM emprunt WHERE id = :id");
    $stmt_old_values->bindParam(':id', $id);
    $stmt_old_values->execute();
    $old_values = $stmt_old_values->fetch(PDO::FETCH_ASSOC);

    // Vérifier si les valeurs soumises sont identiques aux anciennes valeurs
    if ($new_date_deb == $old_values['date_deb'] && $new_date_fin == $old_values['date_fin'] && $new_ref == $old_values['ref'] && $new_num_adherent == $old_values['num_adherent']) {
        $message = "Aucune modification n'a été apportée.";
    } else {
        // Effectuer la mise à jour des valeurs si des modifications ont été apportées
        $stmt_update = $connexion->prepare("UPDATE emprunt SET date_deb = :date_deb, date_fin = :date_fin, ref = :ref, num_adherent = :num_adherent WHERE id = :id");
        $stmt_update->bindParam(':date_deb', $new_date_deb);
        $stmt_update->bindParam(':date_fin', $new_date_fin);
        $stmt_update->bindParam(':ref', $new_ref);
        $stmt_update->bindParam(':num_adherent', $new_num_adherent);
        $stmt_update->bindParam(':id', $id);
        $stmt_update->execute();

        // Vérifier si la mise à jour a été effectuée avec succès
        if ($stmt_update->rowCount() > 0) {
            $message = "La mise à jour a été effectuée avec succès.";
        } else {
            $message = "La mise à jour a échoué.";
        }
    }
}

// Vérification si le formulaire de mise à jour est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si le bouton "Supprimer" est cliqué
    if (isset($_POST['delete'])) {
        // Traitement de la suppression de l'emprunt
        $id = $_POST['id'];

        // Vérifier d'abord si le livre est actuellement emprunté
        $stmt_check_emprunt = $connexion->prepare("SELECT * FROM emprunt WHERE ref = :ref AND date_fin >= CURDATE()");
        $stmt_check_emprunt->bindParam(':ref', $_POST['ref']);
        $stmt_check_emprunt->execute();
        $emprunt_existe = $stmt_check_emprunt->fetch();

        if ($emprunt_existe) {
            $message = "Ce livre est actuellement emprunté et ne peut pas être supprimé.";
        } else {
            try {
                // Supprimer l'emprunt de la base de données
                $stmt = $connexion->prepare("DELETE FROM emprunt WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();

                // Vérifier si la suppression a été effectuée avec succès
                if ($stmt->rowCount() > 0) {
                    $message = "L'emprunt a été supprimé avec succès.";
                } else {
                    $message = "La suppression de l'emprunt a échoué.";
                }
            } catch (PDOException $e) {
                // Afficher les erreurs SQL
                echo "Erreur SQL: " . $e->getMessage();
            }
        }
    }
}

// Ajout d'un nouvel emprunt
if (isset($_POST['add_emprunt'])) {
    $ref = $_POST['ref'];
    $date_deb = date('Y-m-d', strtotime($_POST['date_deb']));
    $date_fin = date('Y-m-d', strtotime($_POST['date_fin']));
    $num_adherent = $_POST['num_adherent'];

    // Vérifier si la référence du livre existe
    $stmt_check_ref = $connexion->prepare("SELECT COUNT(*) FROM livre WHERE ref = :ref");
    $stmt_check_ref->bindParam(':ref', $ref);
    $stmt_check_ref->execute();
    $ref_exists = $stmt_check_ref->fetchColumn();

    // Vérifier si l'adhérent existe
    $stmt_check_adherent = $connexion->prepare("SELECT COUNT(*) FROM adherent WHERE num_adherent = :num_adherent");
    $stmt_check_adherent->bindParam(':num_adherent', $num_adherent);
    $stmt_check_adherent->execute();
    $adherent_exists = $stmt_check_adherent->fetchColumn();

    if (!$ref_exists) {
        $message = "La référence du livre n'existe pas.";
    } elseif (!$adherent_exists) {
        $message = "Le numéro d'adhérent n'existe pas.";
    } else {
        // Vérifier si le livre est disponible avant d'ajouter l'emprunt
        $stmt_check_disponibilite = $connexion->prepare("SELECT * FROM emprunt WHERE ref = :ref AND date_fin >= CURDATE()");
        $stmt_check_disponibilite->bindParam(':ref', $ref);
        $stmt_check_disponibilite->execute();
        $livre_emprunte = $stmt_check_disponibilite->fetch();

        if ($livre_emprunte) {
            $message = "Ce livre est actuellement emprunté et ne peut pas être ajouté à un nouvel emprunt.";
        } else {
            // Ajouter l'emprunt à la base de données
            $stmt = $connexion->prepare("INSERT INTO emprunt (ref, date_deb, date_fin, num_adherent) VALUES (:ref, :date_deb, :date_fin, :num_adherent)");
            $stmt->bindParam(':ref', $ref);
            $stmt->bindParam(':date_deb', $date_deb);
            $stmt->bindParam(':date_fin', $date_fin);
            $stmt->bindParam(':num_adherent', $num_adherent);
            $stmt->execute();

            // Vérifier si l'ajout a été effectué avec succès
            if ($stmt->rowCount() > 0) {
                $message = "Le nouvel emprunt a été ajouté avec succès.";
            } else {
                $message = "L'ajout du nouvel emprunt a échoué.";
            }
        }
    }
}


// Récupération des données d'emprunts depuis la base de données
$stmt = $connexion->query("SELECT emprunt.id, emprunt.ref, livre.titre, DATE_FORMAT(emprunt.date_deb, '%Y-%m-%d') as date_deb, DATE_FORMAT(emprunt.date_fin, '%Y-%m-%d') as date_fin, emprunt.num_adherent FROM emprunt INNER JOIN livre ON emprunt.ref = livre.ref");
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Gestion des Emprunts</title>
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

    <h2>Gestion des Emprunts</h2>
    <div class="gestionEmprunts">
        <h2>Ajouter un nouvel emprunt</h2>
        <form method="POST">
            <input type="text" name="ref" placeholder="Référence Livre" required>
            <input type="date" name="date_deb" placeholder="Date de début" required>
            <input type="date" name="date_fin" placeholder="Date de fin" required>
            <input type="text" name="num_adherent" placeholder="Numéro Adhérent" required>
            <button type="submit" name="add_emprunt">Ajouter</button>
        </form>

        <h2>Liste des emprunts</h2>
        <?php if (!empty($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Référence Livre</th>
                    <th>Nom Livre</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Numéro Adhérent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emprunts as $emprunt) : ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $emprunt['id']; ?>">
                            <td><?php echo $emprunt['id']; ?></td>
                            <td><input type="text" name="ref" value="<?php echo $emprunt['ref']; ?>"></td>
                            <td><?php echo $emprunt['titre']; ?></td>
                            <td><input type="date" name="date_deb" value="<?php echo $emprunt['date_deb']; ?>"></td>
                            <td><input type="date" name="date_fin" value="<?php echo $emprunt['date_fin']; ?>"></td>
                            <td><input type="text" name="num_adherent" value="<?php echo $emprunt['num_adherent']; ?>"></td>
                            <td>
                                <button type="submit" name="update">Mettre à jour</button>
                                <button type="submit" name="delete">Supprimer</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    

    <?php
    // Requête SQL pour récupérer la liste des adhérents
    $stmt_adherents = $connexion->query("SELECT * FROM adherent");
    $adherents = $stmt_adherents->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier si le formulaire a été soumis
    if (isset($_POST['adherent'])) {
        // Récupérer l'identifiant de l'adhérent sélectionné
        $selected_adherent = $_POST['adherent'];

        // Requête SQL pour récupérer les emprunts de l'adhérent sélectionné
        $stmt_emprunts_adherent = $connexion->prepare("SELECT * FROM emprunt WHERE num_adherent = :num_adherent");
        $stmt_emprunts_adherent->bindParam(':num_adherent', $selected_adherent);
        $stmt_emprunts_adherent->execute();
        $emprunts_adherent = $stmt_emprunts_adherent->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>
    <div class="emprunts_adherents">
    <!-- Affichage du formulaire avec la liste déroulante des adhérents -->
    <form method="POST">
        <label for="adherent">Sélectionnez un adhérent pour voir sa liste d'emprunts:</label>
        <select name="adherent" id="adherent">
            <?php foreach ($adherents as $adherent) : ?>
                <option value="<?php echo $adherent['num_adherent']; ?>"><?php echo $adherent['nom'] . ' ' . $adherent['prenom']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Afficher les emprunts</button>
    </form>
    </div>

    <!-- Affichage des emprunts de l'adhérent sélectionné -->
    <?php if (isset($emprunts_adherent)) : ?>
        <div class="emprunts_adherents">
            <h2>Emprunts de l'adhérent</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Emprunt</th>
                        <th>Référence Livre</th>
                        <th>Nom Livre</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <!-- Ajoutez d'autres en-têtes de colonne selon vos besoins -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emprunts_adherent as $emprunt) : ?>
                        <tr>
                            <td><?php echo $emprunt['id']; ?></td>
                            <td><?php echo $emprunt['ref']; ?></td>
                            <!-- Ici, il faut récupérer le titre du livre à partir de la référence et l'afficher -->
                            <?php
                            // Requête SQL pour récupérer le titre du livre à partir de la référence
                            $stmt_titre_livre = $connexion->prepare("SELECT titre FROM livre WHERE ref = :ref");
                            $stmt_titre_livre->bindParam(':ref', $emprunt['ref']);
                            $stmt_titre_livre->execute();
                            $titre_livre = $stmt_titre_livre->fetchColumn();
                            ?>
                            <td><?php echo $titre_livre; ?></td>
                            <td><?php echo $emprunt['date_deb']; ?></td>
                            <td><?php echo $emprunt['date_fin']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</div>

    <a href="back-office.php"><button class="menuBloc">Retour au Back Office</button></a>

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
