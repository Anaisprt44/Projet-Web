-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mer. 03 avr. 2024 à 07:46
-- Version du serveur : 10.10.2-MariaDB
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bibliocat`
--

-- Structure de la table `adherent`
--

DROP TABLE IF EXISTS `adherent`;
CREATE TABLE IF NOT EXISTS `adherent` (
  `num_adherent` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `date_inscription` date DEFAULT NULL,
  `user_login` varchar(255) DEFAULT NULL,
  `user_mdp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`num_adherent`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Déchargement des données de la table `adherent`

INSERT INTO `adherent` (`num_adherent`, `nom`, `prenom`, `mail`, `date_inscription`, `user_login`, `user_mdp`) VALUES
(1, 'compteAdmin', '', 'compteAdmin@email.com', '2016-02-14', 'compteAdmin', 'f1402c965e22dc0b586dd075b2acd01041b8b1f660f4178fc6c8624a6d225fce'),
(2, 'Doe', 'John', 'john.doe@email.com', '2023-01-15', 'jdoe', 'd30a5f57532a603697ccbb51558fa02ccadd74a0c499fcf9d45b33863ee1582f'),
(3, 'Smith', 'Alice', 'alice.smith@email.com', '2023-02-20', 'asmith', '9abe8c76b211c6e1f46a1e91a30821aecd2f4c51f57130c7668438f0e9995071'),
(4, 'Johnson', 'Bob', 'bob.johnson@email.com', '2023-03-10', 'bjohnson', '65faeddc9106788b49b588e0b8752f83e6164b9526fffe22c2ad6c18273b4d32'),
(5, 'Worms', 'Loric', 'loric.worms@email.com', '2024-02-25', 'lworms', '6f7523810ffd44e324c18117b4dbda2f19a9a23f02b8f284fdd43b454b7f0db3'),
(6, 'Tribaleau', 'Titouan', 'titouan.tribaleau@email.com', '2024-02-25', 'ttribaleau', '15be17cc3d0d6990996278e1c5aa6cb66d115466cd22b17dd2435cfed592fe2d');

-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `libelle`) VALUES
(1, 'Roman'),
(2, 'Science-fiction'),
(3, 'Mystère'),
(4, 'Fantasy'),
(5, 'Jeunesse'),
(6, 'Policier'),
(7, 'Manga');

-- Structure de la table `emprunt`
--

DROP TABLE IF EXISTS `emprunt`;
CREATE TABLE IF NOT EXISTS `emprunt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref` int(11) DEFAULT NULL,
  `date_deb` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `num_adherent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`ref`),
  KEY `num_adherent` (`num_adherent`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Déchargement des données de la table `emprunt`
--

INSERT INTO `emprunt` (`ref`, `date_deb`, `date_fin`, `num_adherent`) VALUES
(102, '2023-01-16', '2023-02-15', 2),
(102, '2023-01-16', '2023-02-15', 3),
(102, '2023-02-21', '2023-03-23', 3),
(102, '2023-02-21', '2023-03-23', 4),
(104, '2023-02-21', '2023-03-23', 4),
(110, '2023-02-21', '2023-03-23', 5),
(112, '2023-02-21', '2023-03-23', 6),
(114, '2023-03-11', '2023-04-10', 2),
(112, '2023-03-11', '2023-04-10', 3),
(112, '2023-03-11', '2023-04-10', 4),
(103, '2023-02-21', '2023-03-23', 6),
(128, '2023-03-11', '2023-04-10', 2),
(128, '2023-03-11', '2023-04-10', 3),
(127, '2023-03-11', '2023-04-10', 4),
(125, '2023-02-21', '2023-03-23', 6),
(119, '2023-03-11', '2023-04-10', 2),
(116, '2023-03-11', '2023-04-10', 3),
(116, '2023-03-11', '2023-04-10', 4),
(115, '2023-03-11', '2023-04-10', 2);

-- Structure de la table `livre`
--


DROP TABLE IF EXISTS `livre`;
CREATE TABLE IF NOT EXISTS `livre` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `iban` varchar(255) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `auteur` varchar(255) DEFAULT NULL,
  `langue` varchar(255) DEFAULT NULL,
  `annee` int(11) DEFAULT NULL,
  `id_cat` int(11) DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `descrip` text DEFAULT NULL,
  PRIMARY KEY (`ref`),
  KEY `id_cat` (`id_cat`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`ref`, `iban`, `titre`, `auteur`, `langue`, `annee`, `id_cat`, `photo`, `descrip`) VALUES
('102', 'XYZ456', 'Dune 1', 'Frank Herbert', 'Anglais', 1965, 2, 'Dune_1.png', 'Dune raconte l\'histoire d\'une planète désertique, Arrakis, où une famille noble, les Atreides, prend le contrôle. Là, ils découvrent des secrets et des dangers, y compris une substance précieuse appelée l\'épice. Le jeune Paul Atreides doit faire face à des défis, des intrigues et des mystères dans cet environnement hostile.'),
('103', 'XYZ457', 'Dune 2', 'Frank Herbert', 'Anglais', 1965, 2, 'Dune_2_Le_Messie_de_Dune.png', 'Dune Messiah est la suite du roman Dune. L\'histoire se déroule plusieurs années après les événements du premier livre et suit Paul Muad\'Dib, maintenant empereur de l\'univers, dans son règne sur la galaxie. Paul doit faire face à de nouveaux défis politiques, religieux et personnels alors que son pouvoir est contesté et que des complots se trament contre lui.'),
('104', 'XYZ458', 'Dune 3', 'Frank Herbert', 'Anglais', 1965, 2, 'Dune_3_Les_enfants_de_Dune.png', 'Les Enfants de Dune est la troisième partie de la série Dune. L\'histoire se concentre sur les enfants de Paul Atreides, Leto II et Ghanima, qui sont maintenant confrontés aux conséquences du règne de leur père sur l\'univers. Ils doivent naviguer à travers des intrigues politiques et des machinations, tout en affrontant les défis de leur héritage familial et de leur destin.'),
('105', 'XYZ459', 'Dune 4', 'Frank Herbert', 'Anglais', 1965, 2, 'Dune_4_L_empereur_dieu_de_Dune.png', 'L\'Empereur Dieu de Dune est le quatrième livre de la série. L\'histoire se déroule plusieurs années après les événements du troisième livre et suit les conséquences du règne de Paul Muad Dib en tant qu\'empereur. Le livre explore les ramifications politiques, sociales et religieuses de son règne, ainsi que les défis auxquels sa famille et son empire sont confrontés.'),
('106', 'XYZ460', 'Dune 5', 'Frank Herbert', 'Anglais', 1965, 2, 'Dune_5_Les_hérétiques_de_dune.png', 'Les Hérétiques de Dune est le cinquième livre de la série. L\'histoire se déroule des millénaires après les événements des premiers livres et suit les changements politiques et sociaux sur la planète Arrakis. Le livre explore les tensions entre différentes factions politiques et religieuses, ainsi que les efforts pour préserver la stabilité de l\'univers dans un contexte de changement et de transformation.'),
('107', 'DEF789', 'Sherlock Holmes 1', 'Arthur Conan Doyle', 'Anglais', 1887, 3, 'Sherlock_Holmes_affaire_du_ticket_scandaleux_1.png', 'Un simple diagnostic médical du Dr Watson se révèle être bien plus que cela… La découverte d une poudre mystérieuse sur des vêtements et d un ticket de spectacle très particulier amène Sherlock Holmes à penser que le patient n\'est pas l unique victime d\'un complot de grande ampleur.'),
('108', 'DEF790', 'Sherlock Holmes 2', 'Arthur Conan Doyle', 'Anglais', 1887, 3, 'Sherlock_Holmes_affaire_du_ticket_scandaleux_2.png', 'Alors que Sherlock Holmes et le Dr Watson sont sur la piste du magicien chinois Wu-Jing, le ministre des Colonies Britanniques est à son tour visé.'),
('109', 'RR001', 'Percy Jackson et le voleur de foudre', 'Rick Riordan', 'Français', 2005, 5, 'Percy_Jackson_et_les_Olympiens_T1_Le_Voleur_de_foudre.png', 'Percy Jackson découvre qu il est le fils de Poséidon, le dieu grec de la mer. Lorsque la foudre de Zeus est volée et Percy est accusé du crime, il entreprend un voyage avec ses amis pour retrouver l éclair manquant et prouver son innocence. Au cours de cette quête, Percy affronte des monstres mythologiques, découvre des secrets sur sa propre histoire et se lance dans une aventure épique pour sauver le monde des dieux grecs en colère.'),
('110', 'SC001', 'Hunger Games', 'Suzanne Collins', 'Anglais', 2008, 1, 'Hunger_Games_Tome_01.png', 'Hunger Games se déroule dans un futur dystopique où les districts opprimés de Panem sont contraints de participer à des jeux télévisés mortels. Chaque année, un garçon et une fille de chaque district sont choisis pour s affronter dans une arène jusqu à ce qu un seul survivant reste. L \'histoire suit Katniss Everdeen, une jeune fille du District 12, qui se porte volontaire pour les jeux afin de sauver sa sœur. Dans l arène, elle doit lutter pour sa survie tout en remettant en question le régime oppressif du Capitole.'),
('111', 'SC002', 'Hunger Games 2', 'Suzanne Collins', 'Anglais', 2009, 1, 'Hunger_Games_Tome_02_L_Embrasement.png', 'Dans Hunger Games 2 : L\'Embrasement, la suite de Hunger Games, Katniss Everdeen et Peeta Mellark sont de retour chez eux après avoir remporté les 74e Jeux de la Faim. Cependant, leur victoire n a fait qu attiser les flammes de la rébellion dans les districts opprimés de Panem. Alors que les tensions politiques s intensifient, Katniss et Peeta sont forcés de participer au Tribut des Vainqueurs, une édition spéciale des Jeux de la Faim qui présente les anciens vainqueurs des jeux. Dans cette arène mortelle, Katniss doit faire face à de nouveaux défis et à des choix déchirants pour survivre et lutter contre l oppression du Capitole.'),
('112', 'SC003', 'Hunger Games 3', 'Suzanne Collins', 'Anglais', 2010, 1, 'Hunger_Games_Tome_03_La_Révolte.png', 'Hunger Games 3 : La Révolte conclut la trilogie en suivant Katniss Everdeen alors qu elle devient le symbole de la rébellion contre le Capitole. Le District 13, autrefois caché, se révèle et organise la lutte contre la tyrannie. Katniss doit naviguer à travers des alliances fragiles, des choix moraux difficiles et des batailles intenses alors que la révolte éclate à travers Panem. Ce roman explore les thèmes de la justice, du sacrifice et de la résilience dans une lutte pour la liberté.'),
('113', 'HP001', 'Harry Potter à l ecole des sorciers', 'J.K. Rowling', 'Anglais', 1997, 4, 'Harry_Potter_à_l_Ecole_des_Sorciers.png', 'Dans Harry Potter à l École des Sorciers, le premier livre de la série, nous suivons Harry Potter, un jeune orphelin élevé par des parents adoptifs cruels, qui découvre qu il est un sorcier. Il est invité à rejoindre l école de sorcellerie Poudlard, où il apprend l art de la magie, se fait des amis et découvre des secrets sur son passé et sur le monde des sorciers.'),
('114', 'HP002', 'Harry Potter et la Chambre des secrets', 'J.K. Rowling', 'Anglais', 1998, 4, 'Harry_Potter_et_la_Chambr_des_Secrets.png', 'Dans Harry Potter et la Chambre des Secrets, le deuxième livre de la série, Harry Potter retourne à Poudlard pour sa deuxième année d études de sorcellerie.'),
('115', 'HP003', 'Harry Potter et le Prisonnier d Azkaban', 'J.K. Rowling', 'Anglais', 1999, 4, 'Harry_Potter_et_le_Prisonnier_d_Azkaban.png', 'Dans Harry Potter et le Prisonnier d\'Azkaban, le troisième livre de la série, Harry Potter retourne à l école de sorcellerie de Poudlard pour sa troisième année. L histoire tourne autour de l évasion du célèbre sorcier criminel Sirius Black, qui est censé être après Harry. Alors que Harry apprend la vérité sur son passé et sur ses parents, il doit également faire face à de nouveaux défis magiques et à des dangers plus grands que jamais auparavant.'),
('116', 'HP004', 'Harry Potter et la Coupe de Feu', 'J.K. Rowling', 'Anglais', 2000, 4, 'Harry_Potter_et_la_Coupe_de_Feu.png', 'Dans Harry Potter et la Coupe de Feu, le quatrième livre de la série, l histoire se déroule lors du Tournoi des Trois Sorciers, où des écoles de magie concurrentes s affrontent. Harry Potter est mystérieusement sélectionné pour participer, malgré l âge requis. Alors qu il se prépare pour les épreuves dangereuses, des événements sinistres se déroulent autour de lui, l amenant à découvrir des secrets sombres et à affronter des défis mortels.'),
('117', 'HP005', 'Harry Potter et le Prince de Sang-Mêlé', 'J.K. Rowling', 'Anglais', 2005, 4, 'Harry_Potter_et_le_Prince_de_Sang-Mêlé.png', 'Dans \"Harry Potter et le Prince de Sang-Mêlé\", le sixième livre de la série, Harry Potter et ses amis retournent à Poudlard pour leur sixième année. Le livre explore les secrets du passé de Lord Voldemort à travers les souvenirs que le professeur Dumbledore partage avec Harry. Pendant ce temps, une menace grandit à Poudlard alors que les forces des ténèbres se rassemblent, mettant Harry et ses amis au défi de découvrir la vérité et de se préparer à affronter leur destin.'),
('118', 'HP006', 'Harry Potter et les Reliques de la Mort', 'J.K. Rowling', 'Anglais', 2007, 4, 'Harry_Potter_et_les_Reliques_de_la_Mort.png', 'Dans Harry Potter et les Reliques de la Mort, le septième et dernier tome de la série, Harry Potter et ses amis Hermione Granger et Ron Weasley poursuivent leur quête pour détruire les Horcruxes, des objets qui renferment une partie de l âme de Lord Voldemort. Leur mission les conduit à des découvertes surprenantes, des alliances inattendues et des sacrifices dans leur lutte finale contre les forces du mal.'),
('119', 'LEDMDD', 'L ecole des massacreurs de dragons', 'Kate McMullan', 'Français', 2014, 5, 'L_ecole_des_massacreurs_de_dragons.png', 'Martyrisé par ses frères et exploité par ses parents, le jeune Wiglaf passe son temps à récurer les gamelles et à nourrir les cochons. Mais une affiche placardée sur l arbre à messages du village va changer sa vie. Il va entrer à l École des Massacreurs de Dragons ! Le problème, c est que Wiglaf ne supporte pas d écraser une araignée... Poussez les portes de l École des Massacreurs de Dragons, la célèbre école qui transforme tous les apprentis lecteurs en apprentis massacreurs…'),
('120', 'LMN789', 'Les Disparus de Blackmore', 'Jane Doe', 'Français', 2024, 6, 'Les_Disparus_de_Blackmore.png', 'Les Disparus de Blackmore est un roman captivant qui plonge les lecteurs dans une atmosphère de mystère et de suspense. L\'\nhistoire se déroule dans la paisible ville de Blackmore, où plusieurs résidents ont mystérieusement disparu sans laisser de traces. L\'enquêteur amateur, Sarah Holmes, décide de mener sa propre investigation pour élucider ces étranges disparitions. Au fil de ses recherches, elle découvre des secrets enfouis, des indices troublants et des personnages énigmatiques. Entre rebondissements inattendus et tension croissante, Les Disparus de Blackmore promet une plongée palpitante dans l\'univers du mystère et de l\'intrigue.'),
('121', 'OP0002', 'One Piece - Tome 2', 'Eiichiro Oda', 'Français', 1998, 7, 'One_Piece_Tome_2.png', 'One Piece raconte les aventures d une bande de pirates, menée par le capitaine Monkey D. Luffy (qui a pour ambition de devenir le roi des pirates) et lancée à la recherche du trésor, nommé One Piece, du légendaire roi des pirates Gold Roger, mort sans avoir révélé l emplacement de son butin.'),
('122', 'ABC123', 'Voyage au centre de la terre', 'Jules Verne', 'Français', 1960, 1, 'Voyage_au_centre_de_la_Terre.png', 'Voyage au centre de la Terre est un célèbre roman d\'aventure de science-fiction écrit par Jules Verne. L\'histoire suit les aventures du professeur Otto Lidenbrock, de son neveu Axel et de leur guide Hans, alors qu\'ils entreprennent un voyage audacieux vers le centre de la Terre en suivant un chemin à travers un volcan islandais. Ils découvrent un monde souterrain étonnant, peuplé de créatures préhistoriques et de paysages extraordinaires. Le récit est une combinaison captivante d\'exploration, de suspense et d\'imagination scientifique, offrant aux lecteurs un voyage inoubliable.'),
('126', 'LGDMHW', 'La Guerre des mondes', 'Herbert George Wells', 'Anglais', 1898, 2, 'La-guerre-des-mondes.png', 'La Guerre des mondes est un roman de science-fiction écrit par H. G. Wells, publié en 1898. C\'est une des premières œuvres d\'imagination dont le sujet est l\'humanité confrontée à une espèce extraterrestre hostile, en plus d\'être le reflet de l\'angoisse de l\'époque victorienne et de l\'impérialisme.'),
('127', 'LTDME8', 'Le Tour du monde en quatre-vingts jours\r\n\r\n', 'Jules Verne', 'Français', 1872, 1, 'Le_tour_du_monde_en_80_jours.png', 'Le Tour du monde en quatre-vingts jours est un roman d\'aventures de Jules Verne, publié en 1872. Le roman raconte la course autour du monde d\'un gentleman anglais, Phileas Fogg, qui a fait le pari d\'y parvenir en quatre-vingts jours. Il est accompagné par Jean Passepartout, son fidèle domestique français.'),
('128', 'T100T1', 'The 100 - Tome 01', 'Kass Morgan', 'Français', 2014, 2, 'The_100_Tome1.png', 'Depuis qu\'une guerre nucléaire a ravagé la planète, l\'humanité s\'est réfugiée dans des stations spatiales en orbite à des milliers de kilomètres de sa surface radioactive. Aujourd\'hui, cent jeunes criminels sont envoyés en mission périlleuse : recoloniser la Terre. Cela peut leur donner une chance de repartir de zéro... ou de mourir dès leur arrivée.\r\nClarke a été arrêtée pour trahison, mais son véritable crime continue de la hanter au quotidien. Wells, le fils du Chancelier, est venu sur Terre pour ne pas être séparé d\'elle, cette fille qu\'il aime plus que tout. Mais saura-t-elle un jour pardonner son parjure aux conséquences fatales ? Bellamy, au tempérament de feu, a tout risqué pour rejoindre Octavia à bord de la navette : tous deux sont les seuls frères et soeurs que compte encore le genre humain. Glass, elle, a accompli la manoeuvre inverse et est parvenue à rester à bord de la station. Elle va vite comprendre que les dangers qui la guettent sont au moins aussi nombreux que sur Terre.\r\nFace à un monde hostile où chacun reste rongé par la culpabilité, les 100 vont devoir se battre pour survivre. Ils n\'ont rien de héros, et pourtant, ils pourraient bien être le dernier espoir de l\'humanité...'),
('125', 'XYZ123', '1984', 'George Orwell', 'Anglais', 1949, 1, '1984.png', '1984 est un roman dystopique de George Orwell qui décrit une société totalitaire où la liberté individuelle est complètement écrasée au nom du contrôle gouvernemental absolu. Le livre suit le protagoniste Winston Smith alors qu\'il tente de se rebeller contre le régime oppressif en place, dirigé par le mystérieux Big Brother. À travers son voyage, Orwell explore des thèmes tels que la surveillance étatique, la manipulation de l\'information, et la lutte pour la vérité et la liberté.'),
('129', 'T100T2', 'The 100 - Tome 02', 'Kass Morgan', 'Français', 2020, 2, 'The_100_Tome2.png', 'Le deuxième livre de la série \"The 100\" de Kass Morgan poursuit l\'histoire de 100 jeunes délinquants envoyés sur Terre depuis une station spatiale en ruine pour déterminer si la planète est à nouveau habitable après une apocalypse nucléaire. Dans ce tome, les adolescents font face à de nouveaux défis alors qu\'ils explorent davantage la Terre, découvrant des factions humaines en conflit et des secrets sur leur propre passé. Pendant ce temps, sur la station spatiale, les survivants luttent pour leur survie et essaient de comprendre les mystères qui entourent la Terre. Entre intrigues politiques, relations interpersonnelles et dangers inhérents à un monde post-apocalyptique, le livre explore les thèmes de la survie, de la trahison et de la résilience dans un contexte de science-fiction captivant.');
COMMIT;


-- Contraintes pour la table `emprunt` --
ALTER TABLE `emprunt`
  ADD CONSTRAINT `emprunt_ibfk_1` FOREIGN KEY (`ref`) REFERENCES `livre` (`ref`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `emprunt_ibfk_2` FOREIGN KEY (`num_adherent`) REFERENCES `adherent` (`num_adherent`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Contraintes pour la table `livre` --
ALTER TABLE `livre`
  ADD CONSTRAINT `livre_ibfk_1` FOREIGN KEY (`id_cat`) REFERENCES `categorie` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

COMMIT;

-- Ajouter l'auto-incrémentation à la colonne ref
ALTER TABLE livre MODIFY COLUMN ref INT AUTO_INCREMENT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
