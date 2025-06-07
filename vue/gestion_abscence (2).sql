-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 07 juin 2025 à 11:38
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_abscence`
--

-- --------------------------------------------------------

--
-- Structure de la table `abscences`
--

DROP TABLE IF EXISTS `abscences`;
CREATE TABLE IF NOT EXISTS `abscences` (
  `id_abscence` int NOT NULL AUTO_INCREMENT,
  `dates` varchar(255) NOT NULL,
  `periode` varchar(255) NOT NULL,
  `matiere` varchar(255) NOT NULL,
  `statut` varchar(255) NOT NULL DEFAULT 'non_justifier',
  `id_etudiant` int NOT NULL,
  PRIMARY KEY (`id_abscence`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `abscences`
--

INSERT INTO `abscences` (`id_abscence`, `dates`, `periode`, `matiere`, `statut`, `id_etudiant`) VALUES
(1, '2025-05-07', '10H-12H', 'Anglais', 'justifier', 1),
(2, '2025-05-07', '13H-15H', 'Mathématiques', 'justifier', 1),
(3, '2025-05-09', '8H-10H', 'Anglais', 'justifier', 1),
(4, '2025-05-09', '8H-10H', 'Anglais', 'justifier', 2),
(5, '2025-05-09', '8H-10H', 'Anglais', 'non_justifier', 3),
(6, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(7, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(8, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(9, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(10, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(11, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(12, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(13, '2025-05-09', '15H-17H', 'Informatique', 'justifier', 2),
(14, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 2),
(15, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 3),
(16, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 2),
(17, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 3),
(18, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 2),
(19, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 3),
(20, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 2),
(21, '2025-05-06', '8H-10H', 'deep learning', 'non_justifier', 3),
(22, '2025-05-09', '15H-17H', 'analyse', 'justifier', 2),
(23, '2025-05-16', '13H-15H', 'C', 'non_justifier', 1),
(24, '2025-05-16', '13H-15H', 'C', 'non_justifier', 2),
(25, '2025-05-17', '8H-10H', 'analyse', 'non_justifier', 1),
(26, '2025-05-17', '8H-10H', 'analyse', 'justifier', 2),
(27, '2025-05-15', '8H-10H', 'analyse', 'non_justifier', 1),
(28, '2025-05-15', '8H-10H', 'analyse', 'justifier', 2),
(29, '2025-05-16', '8H-10H', 'C', 'non_justifier', 1),
(30, '2025-05-16', '8H-10H', 'C', 'non_justifier', 2),
(31, '2025-05-16', '8H-10H', 'C', 'non_justifier', 5),
(32, '2025-05-16', '8H-10H', 'C', 'non_justifier', 6),
(33, '2025-05-18', '8H-10H', 'web', 'non_justifier', 1),
(34, '2025-05-18', '8H-10H', 'web', 'non_justifier', 2),
(35, '2025-05-18', '8H-10H', 'web', 'non_justifier', 5),
(36, '2025-05-18', '8H-10H', 'web', 'non_justifier', 6),
(37, '2025-05-17', '8H-10H', 'mecanique', 'non_justifier', 1),
(38, '2025-05-17', '8H-10H', 'mecanique', 'non_justifier', 2),
(39, '2025-05-17', '8H-10H', 'mecanique', 'non_justifier', 5),
(40, '2025-05-17', '8H-10H', 'mecanique', 'non_justifier', 6),
(41, '2025-05-22', '8H-10H', 'analyse', 'non_justifier', 1),
(42, '2025-05-22', '8H-10H', 'analyse', 'non_justifier', 2),
(43, '2025-05-22', '8H-10H', 'analyse', 'non_justifier', 5),
(44, '2025-05-22', '8H-10H', 'analyse', 'non_justifier', 6),
(45, '2025-05-16', '8H-10H', 'algebre', 'non_justifier', 1),
(46, '2025-05-16', '8H-10H', 'algebre', 'non_justifier', 2),
(47, '2025-05-16', '8H-10H', 'algebre', 'non_justifier', 5),
(48, '2025-05-16', '8H-10H', 'algebre', 'non_justifier', 6),
(49, '2025-05-17', '8H-10H', 'web', 'non_justifier', 1),
(50, '2025-05-17', '8H-10H', 'web', 'non_justifier', 2),
(51, '2025-05-17', '8H-10H', 'web', 'non_justifier', 5),
(52, '2025-05-17', '8H-10H', 'web', 'non_justifier', 6),
(53, '2025-05-21', '8H-10H', 'analyse', 'non_justifier', 1),
(54, '2025-05-21', '8H-10H', 'analyse', 'non_justifier', 2),
(55, '2025-05-21', '8H-10H', 'analyse', 'non_justifier', 5),
(56, '2025-05-21', '8H-10H', 'analyse', 'non_justifier', 6),
(57, '2025-05-17', '8H-10H', 'analyse', 'non_justifier', 2),
(58, '2025-05-17', '8H-10H', 'analyse', 'non_justifier', 5),
(59, '2025-05-17', '8H-10H', 'algebre', 'justifier', 7);

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `filiere` varchar(255) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id_admin`, `nom`, `filiere`, `email`, `mot_de_passe`) VALUES
(1, 'admin', 'prepa', 'admin@gmail.com', '12345678');

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

DROP TABLE IF EXISTS `administrateur`;
CREATE TABLE IF NOT EXISTS `administrateur` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passworde` varchar(255) NOT NULL,
  `filiere` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`id_admin`, `nom`, `email`, `passworde`, `filiere`) VALUES
(1, 'admin', 'admin@gmail.com', '12345678', 'prepa');

-- --------------------------------------------------------

--
-- Structure de la table `justificatifs`
--

DROP TABLE IF EXISTS `justificatifs`;
CREATE TABLE IF NOT EXISTS `justificatifs` (
  `id_justificatif` int NOT NULL AUTO_INCREMENT,
  `dates` varchar(255) NOT NULL,
  `motif` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `statut` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `files` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_etudiant` int NOT NULL,
  `periode` varchar(255) NOT NULL DEFAULT '8H-10H',
  `date_abscence` varchar(255) NOT NULL DEFAULT '02/02/2025',
  PRIMARY KEY (`id_justificatif`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `justificatifs`
--

INSERT INTO `justificatifs` (`id_justificatif`, `dates`, `motif`, `commentaire`, `statut`, `files`, `id_etudiant`, `periode`, `date_abscence`) VALUES
(14, '2025-05-08', 'medical', 'ZZaaxa', 'valider', '681c6b190b247_vlcsnap-2025-05-04-19h21m12s177.png', 1, ',10H-12H,13-15H,15H-17H', '2025-05-08'),
(15, '2025-05-08', 'medical', 'adsdgdg', 'valider', '681c6b64a8039_vlcsnap-2025-05-04-19h21m12s177.png', 1, ',8H-10H,10H-12H,13-15H,15H-17H', '2025-05-09'),
(17, '2025/05/08', 'family', 'je ne suis pas seuk', '2025-05-08', '681c707eb2cb0_vlcsnap-2025-05-04-19h21m17s569.png', 1, ',10H-12H,13-15H,15H-17H', '2025-05-08'),
(18, '2025/05/08', 'medical', 'je ne suis pas mort', 'valider', '681c7738271a0_vlcsnap-2025-05-04-19h21m12s177.png', 1, ',10H-12H', '2025-05-07'),
(19, '2025/05/08', 'medical', 'je ne suis pas mort', 'valider', '681c7804a61b9_vlcsnap-2025-05-04-19h21m03s295.png', 1, ',8H-10H,10H-12H', '2025-05-07'),
(25, '2025/05/08', 'medical', 'wewqeqe', 'rejete', '681c879d3c675_Capture.PNG', 1, ',10H-12H,13-15H,15H-17H', '2025-05-07'),
(26, '2025/05/08', 'medical', 'asasdad', 'valider', '681c9050cf4ed_capture1.PNG', 1, ',13-15H,15H-17H', '2025-05-07'),
(27, '2025/05/09', 'medical', 'ajdohshpdjhkad', 'rejete', '681db07e70d37_Capture.PNG', 1, ',10H-12H,13-15H', '2025-05-07'),
(28, '2025/05/09', 'medical', 'zadsfdf', 'rejete', '681dd065a021b_Capture8.PNG', 1, ',13-15H,15H-17H', '2025-05-07'),
(29, '2025/05/09', 'family', 'ASASAasasdsdc', 'rejete', '681dd0d97ed37_Capture3.PNG', 1, ',10H-12H,13H-15H,15H-17H', '2025-05-07'),
(30, '2025/05/09', 'transport', 'je n avais pas l argent ', 'valider', '681dd29caddb3_Capture3.PNG', 1, ',8H-10H', '2025-05-09'),
(31, '2025/05/16', 'medical', 'KSDJSKFJSFKLJ', 'valider', '6826ec6488c31_2.PNG', 2, ',15H-17H', '2025-05-09'),
(32, '2025/05/16', 'medical', 'j e anbadhjbadkj sfsf', 'valider', '682776d020fa0_acceuilAdmin.PNG', 2, ',8H-10H', '2025-05-17'),
(33, '2025/05/16', 'medical', 'asasad', 'valider', '682778f4377bd_4.PNG', 2, ',8H-10H', '2025-05-15'),
(34, '2025/05/17', 'medical', 'JASHJAADHJ', 'valider', '682874c3792a9_3.PNG', 7, ',8H-10H', '2025-05-17');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

DROP TABLE IF EXISTS `matieres`;
CREATE TABLE IF NOT EXISTS `matieres` (
  `code_matiere` int NOT NULL AUTO_INCREMENT,
  `nom_matiere` varchar(255) NOT NULL,
  `emailProf` varchar(255) NOT NULL,
  PRIMARY KEY (`code_matiere`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`code_matiere`, `nom_matiere`, `emailProf`) VALUES
(1, 'algebre', 'djoumessiivan2006@gmail.com'),
(2, 'analyse', 'djoumessiivan2006@gmail.com'),
(3, 'mecanique', 'djoumessiivan2006@gmail.com'),
(4, 'java', 'djoumessiivan2006@gmail.com'),
(5, 'C', 'djoumessiivan2006@gmail.com'),
(6, 'web', 'djoumessiivan2006@gmail.com'),
(7, 'deep learning', 'djoumessiivan2006@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `professeur`
--

DROP TABLE IF EXISTS `professeur`;
CREATE TABLE IF NOT EXISTS `professeur` (
  `id_prof` int NOT NULL,
  `nom_prof` int NOT NULL,
  `email_prof` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_etudiant` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `filiere` varchar(255) NOT NULL,
  `niveau` int NOT NULL,
  `classe` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'etudiant',
  PRIMARY KEY (`id_etudiant`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_etudiant`, `nom`, `prenom`, `email`, `mot_de_passe`, `filiere`, `niveau`, `classe`, `role`) VALUES
(1, 'cheun', 'cheun', 'admin@gmail.com', '12345678', 'prepa', 1, 'A', 'delegue'),
(2, 'test', 'test', 'test@gmail.com', '12345678', 'prepa', 1, 'A', 'etudiant'),
(5, 'CHEUN', 'cheun', 'cheun@gmail.com', '12345678', 'prepa', 1, 'A', 'etudiant'),
(7, 'djoumessi', 'brandon', 'djoumessiivan2006@gmail.com', '12345678', 'prepa', 1, 'A', 'etudiant');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
