<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioCAT</title>
    <link href="styles.css" rel="stylesheet">
    <!-- Ajoutez du code JavaScript pour afficher une alerte -->
    <script>
        function afficherMessage() {
            alert("Merci pour votre message, nous vous re-contacterons !");
        }
    </script>
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

            <a href="galerie.php">
                <button class="menuBloc">Galerie</button>
            </a>
            <a href="contact.php" class="chat-link">
                <button class="menuBloc">Contact</button>
                <img src="Images/mochi-peach.gif" alt="chat" id="chat">
            </a>
            
            <a href="connexionUtilisateur.php" class="chat-link">
                <button class="menuBloc">Connexion</button>
                
            </a>
        </div>            
    </header>

    <h2>Pour nous contacter</h2>
    <!-- Zone Contact formulaire -->
    <div class="bloc-contact">
        
        <div class="sous-bloc-contact">
            <div class="bloc-contact-coordonnees">
                <h2>Nos coordonnées</h2>
                <p>Adresse: 123 Rue de la Rue, Nantes</p>
                <p>Téléphone: +123 456 789</p>
                <p>Mail: bibliocat.contact@gmail.com</p>
            </div>          
            <div class="bloc-contact-formulaire">
                <form action="#" method="post" id="contact-form" onsubmit="afficherMessage(); return false;">
                    <h2>Contactez-nous</h2>         
                    
                        <div>
                            <label for="email">Email :</label>
                        </div>
                        <div>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div>
                            <label for="message">Message :</label>
                        </div>
                        <div>
                            <textarea id="message" name="message" rows="4" required></textarea>
                        </div>
                        <div>
                            <button type="submit" id="bouton-mail">Envoyer</button>
                        </div>
                  
                </form>
            </div>
        </div>
        <div class="bloc-contact-map">
            <!-- Ajoutez ici le code nécessaire pour intégrer le widget Google Maps -->
            <!-- (à remplacer par notre code Google Maps) -->
            <iframe src="https://www.google.com/maps/d/embed?mid=1UKRIVhKyi8ghgPsjCmbroY0ildXB4fg&ehbc=2E312F&noprof=1"></iframe>
        </div>
        
    </div>


    <div class="footer-clean">
        <footer>
            <div class="container">
                <div class="item social">
                    <!-- Ajoutez icônes de réseaux sociaux -->
                    
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
