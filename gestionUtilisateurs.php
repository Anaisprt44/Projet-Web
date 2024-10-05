<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté en tant que "compteAdmin"
$isCompteAdmin = isset($_SESSION['user_username']) && $_SESSION['user_username'] === 'compteAdmin';

// Connexion à la base de données
$dbhost = "localhost";
$dbport = 3307;
$dbname = "bibliocat";
$dbuser = "compteAdmin";
$dbpasswd = "joliverie";

try {
    $connexion = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $dbuser, $dbpasswd);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$message = "";

// Suppression de l'adhérent ou de l'utilisateur
if (isset($_POST['delete'])) {
    $num_adherent = $_POST['num_adherent'];
    $stmt = $connexion->prepare("DELETE FROM adherent WHERE num_adherent = :num_adherent");
    $stmt->bindParam(':num_adherent', $num_adherent);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $message = "L'adhérent a été supprimé avec succès.";
    } else {
        $message = "La suppression de l'adhérent a échoué.";
    }
}

// Mise à jour de l'adhérent ou de l'utilisateur
if (isset($_POST['update'])) {
    $num_adherent = $_POST['num_adherent'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $date_inscription = $_POST['date_inscription'];
    $user_login = $_POST['user_login'];
    $user_mdp = $_POST['user_mdp'];

    // Vérifier si les données mises à jour sont identiques aux données existantes dans la base de données
    $stmt_check = $connexion->prepare("SELECT * FROM adherent WHERE num_adherent = :num_adherent");
    $stmt_check->bindParam(':num_adherent', $num_adherent);
    $stmt_check->execute();
    $existing_data = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existing_data['nom'] === $nom && 
        $existing_data['prenom'] === $prenom && 
        $existing_data['mail'] === $email && 
        $existing_data['date_inscription'] === $date_inscription && 
        $existing_data['user_login'] === $user_login && 
        $existing_data['user_mdp'] === $user_mdp) {
        $message = "Aucun changement n'a été effectué.";
    } else {
        // Effectuer la mise à jour des données
        $stmt = $connexion->prepare("UPDATE adherent SET nom = :nom, prenom = :prenom, mail = :email, date_inscription = :date_inscription, user_login = :user_login, user_mdp = :user_mdp WHERE num_adherent = :num_adherent");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':date_inscription', $date_inscription);
        $stmt->bindParam(':user_login', $user_login);
        $stmt->bindParam(':user_mdp', $user_mdp);
        $stmt->bindParam(':num_adherent', $num_adherent);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $message = "Les informations de l'adhérent ont été mises à jour avec succès.";
        } else {
            $message = "La mise à jour des informations de l'adhérent a échoué.";
        }
    }
}


// Ajout d'un nouvel adhérent ou utilisateur
if (isset($_POST['add_adherent'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $user_login = $_POST['user_login'];
    $user_mdp = $_POST['user_mdp'];
    $date_inscription = date("Y-m-d"); // Date d'inscription = date d'aujourd'hui

    $stmt = $connexion->prepare("INSERT INTO adherent (nom, prenom, mail, user_login, user_mdp, date_inscription) VALUES (:nom, :prenom, :email, :user_login, :user_mdp, :date_inscription)");
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_login', $user_login);
    $stmt->bindParam(':user_mdp', $user_mdp);
    $stmt->bindParam(':date_inscription', $date_inscription);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $message = "L'adhérent a été ajouté avec succès.";
    } else {
        $message = "L'ajout de l'adhérent a échoué.";
    }
}


// Récupération des données des adhérents depuis la base de données
$stmt = $connexion->query("SELECT * FROM adherent");
$adherents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Gestion des Adhérents</title>
</head>

<body>
    
<body>
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

    <div class="container">
        <div class="gestionUtilisateurs">
            <h2>Gestion des Adhérents</h2>
            <br>
            
            <?php if (!empty($message)) : ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
            <!-- Formulaire d'ajout d'adhérent -->
            <form method="POST">
                <h3>Ajouter un nouvel adhérent</h3>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="user_login" placeholder="Login" required>
                <input type="password" name="user_mdp" placeholder="Mot de passe" required>
                <button type="submit" name="add_adherent">Ajouter</button>
            </form>
            
            
            
            
            <table>
                <thead>
                    <tr>
                        <th>Numéro d'Adhérent</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Date d'Inscription</th>
                        <th>Login</th>
                        <th>Mot de Passe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adherents as $adherent) : ?>
                        <tr>
                            <form method="POST">
                                <input type="hidden" name="num_adherent" value="<?php echo $adherent['num_adherent']; ?>">
                                <td><?php echo $adherent['num_adherent']; ?></td>
                                <td><input type="text" name="nom" value="<?php echo $adherent['nom']; ?>"></td>
                                <td><input type="text" name="prenom" value="<?php echo $adherent['prenom']; ?>"></td>
                                <td><input type="email" name="email" value="<?php echo $adherent['mail']; ?>"></td>
                                <td><?php echo isset($adherent['date_inscription']) ? $adherent['date_inscription'] : ''; ?></td>
                                <input type="hidden" name="date_inscription" value="<?php echo $adherent['date_inscription']; ?>">
                                <td><input type="text" name="user_login" value="<?php echo $adherent['user_login']; ?>"></td>
                                <td><input type="password" name="user_mdp" value="<?php echo $adherent['user_mdp']; ?>"></td>
                                <td>
                                    <button type="submit" name="update">Modifier</button>
                                    <button type="submit" name="delete">Supprimer</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            
            
            
            

        </div>
         <a href="back-office.php"><button class="menuBloc">Retour au Back Office</button></a>
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
