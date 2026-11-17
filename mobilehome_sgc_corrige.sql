-- phpMyAdmin SQL Dump
-- IMPORTANT - SECURITE ET RECONCILIATION
-- Ce dump conserve les identifiants existants pour ne pas bloquer l'application.
-- Avant toute mise en production, migrer les mots de passe en PHP password_hash()
-- et supprimer les mots de passe en clair après validation de la connexion.
-- Les écarts de stocks/prix sont signalés par les requêtes de contrôle ci-dessous :
-- ils doivent être validés à partir de l'inventaire et des pièces commerciales.
--
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 20 août 2026 à 09:20
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

CREATE DATABASE IF NOT EXISTS `mobilehome_sgc_corrige`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mobilehome_sgc_corrige`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mobilehome_sgc_corrige`
--

-- --------------------------------------------------------

--
-- Structure de la table `table_capacites`
--

DROP TABLE IF EXISTS `table_capacites`;
CREATE TABLE IF NOT EXISTS `table_capacites` (
  `id_capacite` int NOT NULL AUTO_INCREMENT,
  `stockage_go` int NOT NULL,
  `ram_go` int NOT NULL,
  PRIMARY KEY (`id_capacite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `table_capacites` (`id_capacite`, `stockage_go`, `ram_go`) VALUES
(1, 128, 4),
(2, 128, 8),
(3, 256, 8),
(4, 256, 12),
(5, 256, 16),
(6, 512, 12),
(7, 512, 16);

-- --------------------------------------------------------

--
-- Structure de la table `table_clients`
--

DROP TABLE IF EXISTS `table_clients`;
CREATE TABLE IF NOT EXISTS `table_clients` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `telephone` varchar(30) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT 'Lomé',
  `cumul_achats` decimal(12,2) DEFAULT '0.00',
  `statut_fidelite` varchar(50) DEFAULT 'Standard',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_clients`
--

INSERT INTO `table_clients` (`id_client`, `nom`, `prenom`, `telephone`, `email`, `localisation`, `cumul_achats`, `statut_fidelite`, `date_creation`) VALUES
(1, 'Bamba', 'Fatoumata', '+228 90 12 34 56', 'fatoumata.bamba@gmail.com', 'Lomé Tokoin', 1770000.00, 'VIP Gold', '2026-07-29 07:00:40'),
(2, 'Lawson', 'Koffi', '+228 91 00 11 22', 'koffi.lawson@hotmail.com', 'Lomé Adidogomé', 980000.00, 'VIP Silver', '2026-07-29 07:00:40'),
(3, 'Diallo', 'Moussa', '+228 99 88 77 66', 'moussa.diallo@yahoo.fr', 'Lomé Agbalépédogan', 455000.00, 'Standard', '2026-07-29 07:00:40'),
(4, 'Dogbé', 'Afiwa', '+228 92 33 44 55', 'afiwa.dogbe@gmail.com', 'Lomé Bè', 245000.00, 'Standard', '2026-07-29 07:00:40'),
(5, 'Amouzou', 'Kodjo', '+228 93 77 88 99', 'kodjo.amouzou@yahoo.fr', 'Lomé Totsi', 650000.00, 'Standard', '2026-07-29 07:00:40'),
(6, 'Johnson', 'Eya', '+228 96 11 22 33', 'eya.johnson@outlook.com', 'Lomé Kégué', 1210000.00, 'VIP Silver', '2026-07-29 07:00:40'),
(7, 'KOUMAGNON', 'Calice', '+228 00 00 00 00', 'calice@gmail.com', 'COTONOU', 0.00, 'Standard', '2026-08-01 16:38:53');

-- --------------------------------------------------------

--
-- Structure de la table `table_entreprise`
--

DROP TABLE IF EXISTS `table_entreprise`;
CREATE TABLE IF NOT EXISTS `table_entreprise` (
  `id_entreprise` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL DEFAULT 'JY SHOP STORE',
  `slogan` varchar(255) DEFAULT 'PC HP - 8e a 13e generation',
  `adresse` varchar(255) DEFAULT 'À compléter',
  `tel1` varchar(50) DEFAULT '+229 90 38 90 62',
  `tel2` varchar(50) DEFAULT '',
  `whatsapp` varchar(50) DEFAULT '+229 90 38 90 62',
  `email` varchar(150) DEFAULT '',
  `logo_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_entreprise`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_entreprise`
--

INSERT INTO `table_entreprise` (`id_entreprise`, `nom`, `slogan`, `adresse`, `tel1`, `tel2`, `whatsapp`, `email`, `logo_url`) VALUES
(1, 'JY SHOP STORE', 'PC HP - 8e a 13e generation', 'À compléter', '+229 90 38 90 62', '', '+229 90 38 90 62', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `table_fournisseurs`
--

DROP TABLE IF EXISTS `table_fournisseurs`;
CREATE TABLE IF NOT EXISTS `table_fournisseurs` (
  `id_fournisseur` int NOT NULL AUTO_INCREMENT,
  `nom_societe` varchar(150) NOT NULL,
  `nom_contact` varchar(100) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `produits_fournis` text,
  PRIMARY KEY (`id_fournisseur`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_fournisseurs`
--

INSERT INTO `table_fournisseurs` (`id_fournisseur`, `nom_societe`, `nom_contact`, `telephone`, `email`, `produits_fournis`) VALUES
(1, 'Dubai Phone Wholesale LLC', 'Mr. Tariq Al-Mansoor', '+971 50 123 4567', 'sales@dubaiphone.ae', 'iPhones neufs certifiés & accessoires Apple d\'origine'),
(2, 'HK Telecom Trading Co.', 'Mme. Li Zhang', '+852 9876 5432', 'zhang@hktelecom.hk', 'Smartphones Xiaomi, Redmi et Poco officiels'),
(3, 'Samsung Gulf Electronics FZE', 'Mr. Karim Haddad', '+971 4 888 9900', 'info@samsunggulf.ae', 'Gamme complète Samsung Galaxy S & A'),
(4, 'Transsion Official Togo (Tecno/Infinix)', 'Représentant Togo', '+228 90 00 11 22', 'togo@transsion.com', 'Smartphones Tecno Camon, Infinix Note & Itel'),
(5, 'Non soluta qui ducim', 'Voluptates dolor vol', '+1 (614) 925-7984', 'bifepan@mailinator.com', 'Deserunt deleniti vo');

-- --------------------------------------------------------

--
-- Structure de la table `table_imei`
--

DROP TABLE IF EXISTS `table_imei`;
CREATE TABLE IF NOT EXISTS `table_imei` (
  `id_imei` int NOT NULL AUTO_INCREMENT,
  `id_produit` int NOT NULL,
  `imei1` varchar(20) NOT NULL,
  `imei2` varchar(20) DEFAULT NULL,
  `etat_batterie_pct` int DEFAULT '100',
  `statut` enum('En Stock','Vendu','Reçu Troc') DEFAULT 'En Stock',
  `date_entree` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_imei`),
  UNIQUE KEY `imei1` (`imei1`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_imei`
--

INSERT INTO `table_imei` (`id_imei`, `id_produit`, `imei1`, `imei2`, `etat_batterie_pct`, `statut`, `date_entree`) VALUES
(1, 1, '351029678528627', NULL, 88, 'Reçu Troc', '2026-07-29 06:33:44');

-- --------------------------------------------------------

--
-- Structure de la table `table_lignes_vente`
--

DROP TABLE IF EXISTS `table_lignes_vente`;
CREATE TABLE IF NOT EXISTS `table_lignes_vente` (
  `id_ligne` int NOT NULL AUTO_INCREMENT,
  `id_vente` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `prix_unitaire` decimal(12,2) NOT NULL,
  `benefice_ligne` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_ligne`),
  KEY `id_vente` (`id_vente`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_lignes_vente`
--

INSERT INTO `table_lignes_vente` (`id_ligne`, `id_vente`, `id_produit`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 1, 1, 980000.00),
(2, 2, 4, 1, 790000.00),
(3, 3, 5, 1, 245000.00),
(4, 4, 6, 1, 650000.00),
(5, 5, 2, 1, 680000.00),
(6, 6, 5, 1, 245000.00),
(7, 7, 11, 1, 210000.00),
(8, 8, 11, 2, 210000.00),
(9, 8, 9, 1, 590000.00),
(10, 9, 10, 1, 250000.00),
(11, 9, 4, 1, 790000.00),
(12, 10, 9, 1, 590000.00),
(13, 10, 11, 1, 210000.00),
(14, 10, 2, 1, 680000.00);

-- --------------------------------------------------------

--
-- Structure de la table `table_marques`
--

DROP TABLE IF EXISTS `table_marques`;
CREATE TABLE IF NOT EXISTS `table_marques` (
  `id_marque` int NOT NULL AUTO_INCREMENT,
  `nom_marque` varchar(100) NOT NULL,
  PRIMARY KEY (`id_marque`),
  UNIQUE KEY `nom_marque` (`nom_marque`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_marques`
--

INSERT INTO `table_marques` (`id_marque`, `nom_marque`) VALUES
(1, 'Apple'),
(4, 'Google Pixel'),
(6, 'Infinix'),
(2, 'Samsung'),
(5, 'Tecno'),
(3, 'Xiaomi');

INSERT IGNORE INTO `table_marques` (`nom_marque`) VALUES ('HP');

-- --------------------------------------------------------

--
-- Structure de la table `table_paiements`
--

DROP TABLE IF EXISTS `table_paiements`;
CREATE TABLE IF NOT EXISTS `table_paiements` (
  `id_paiement` int NOT NULL AUTO_INCREMENT,
  `id_vente` int NOT NULL,
  `moyen_paiement` enum('Espèces','Flooz','T-Money','Carte Bancaire') NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `reference_transac` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_paiement`),
  KEY `id_vente` (`id_vente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `table_prix`
--

DROP TABLE IF EXISTS `table_prix`;
CREATE TABLE IF NOT EXISTS `table_prix` (
  `id_prix` int NOT NULL AUTO_INCREMENT,
  `id_produit` int NOT NULL,
  `prix_achat` decimal(12,2) NOT NULL,
  `prix_vente_moyen` decimal(12,2) NOT NULL,
  `prix_min` decimal(12,2) DEFAULT NULL,
  `date_modification` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_prix`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_prix`
--

INSERT INTO `table_prix` (`id_prix`, `id_produit`, `prix_achat`, `prix_vente_moyen`, `prix_min`, `date_modification`) VALUES
(1, 1, 480000.00, 590000.00, 550000.00, '2026-07-28 22:55:09'),
(2, 2, 530000.00, 650000.00, 600000.00, '2026-07-28 22:55:09'),
(3, 3, 190000.00, 250000.00, 230000.00, '2026-07-28 22:55:09'),
(4, 4, 130000.00, 165000.00, 150000.00, '2026-07-28 22:55:09'),
(5, 5, 45000.00, 60000.00, 55000.00, '2026-07-28 22:55:09'),
(6, 6, 850000.00, 980000.00, 930000.00, '2026-07-28 22:55:09'),
(7, 7, 720000.00, 850000.00, 800000.00, '2026-07-28 22:55:09'),
(8, 8, 680000.00, 790000.00, 750000.00, '2026-07-28 22:55:09'),
(9, 9, 460000.00, 550000.00, 520000.00, '2026-07-28 22:55:09'),
(10, 10, 9999999999.99, 9999999999.99, NULL, '2026-07-29 06:33:12');

-- --------------------------------------------------------

--
-- Structure de la table `table_produits`
--

DROP TABLE IF EXISTS `table_produits`;
CREATE TABLE IF NOT EXISTS `table_produits` (
  `id_produit` int NOT NULL AUTO_INCREMENT,
  `id_marque` int NOT NULL,
  `id_serie` int DEFAULT NULL,
  `id_capacite` int DEFAULT NULL,
  `nom_produit` varchar(150) NOT NULL,
  `modele_exact` varchar(150) DEFAULT NULL,
  `description` text,
  `stockage_ram` varchar(50) DEFAULT '8 Go RAM / 256 Go SSD',
  `reseau` varchar(20) DEFAULT 'PC',
  `generation` tinyint unsigned DEFAULT NULL,
  `processeur` varchar(120) DEFAULT NULL,
  `carte_graphique` varchar(120) DEFAULT NULL,
  `type_pc` varchar(40) NOT NULL DEFAULT 'Bureautique',
  `prix_vente` decimal(12,2) NOT NULL DEFAULT '0.00',
  `prix_achat` decimal(12,2) DEFAULT '0.00',
  `quantite_stock` int DEFAULT '0',
  `seuil_alerte` int DEFAULT '3',
  `image_url` varchar(255) DEFAULT NULL,
  `garantie` varchar(50) DEFAULT '12 Mois',
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP,
  `couleurs` varchar(255) DEFAULT 'Titane, Noir, Argent',
  PRIMARY KEY (`id_produit`),
  KEY `id_marque` (`id_marque`),
  KEY `id_capacite` (`id_capacite`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_produits`
--

INSERT INTO `table_produits` (`id_produit`, `id_marque`, `nom_produit`, `modele_exact`, `description`, `stockage_ram`, `reseau`, `prix_vente`, `prix_achat`, `quantite_stock`, `seuil_alerte`, `image_url`, `garantie`, `date_ajout`, `couleurs`) VALUES
(1, 1, 'iPhone 16 Pro Max', 'iPhone 16 Pro Max Titanium', 'Flagship Apple avec puce A18 Pro et écran Super Retina XDR 6.9 pouces.', '256Go / 8Go', '5G', 980000.00, 850000.00, 12, 3, 'iphone16.png', '12 Mois Apple', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(2, 1, 'iPhone 15 Pro', 'iPhone 15 Pro Titane Naturel', 'Smartphone Apple premium avec puce A17 Pro et châssis titane.', '128Go / 8Go', '5G', 680000.00, 590000.00, 6, 3, 'iphone16.png', '12 Mois Apple', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(3, 1, 'iPhone 13', 'iPhone 13 Minuit', 'iPhone haute performance, double capteur photo 12MP et excellente autonomie.', '128Go / 4Go', '5G', 420000.00, 350000.00, 2, 3, 'default_phone.png', '12 Mois', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(4, 2, 'Samsung Galaxy S24 Ultra', 'Galaxy S24 Ultra Titanium Gray', 'Le roi Android avec S-Pen intégré, Snapdragon 8 Gen 3 et capteur 200MP.', '512Go / 12Go', '5G', 790000.00, 680000.00, 14, 3, 's24ultra.png', '24 Mois Samsung', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(5, 2, 'Samsung Galaxy A55 5G', 'Galaxy A55 5G Awesome Navy', 'Best-seller milieu de gamme avec finition verre premium et étanchéité IP67.', '256Go / 8Go', '5G', 245000.00, 200000.00, 19, 3, 's24ultra.png', '12 Mois Samsung', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(6, 3, 'Xiaomi 14 Ultra', 'Xiaomi 14 Ultra Black Leica', 'Monstre de la photographie mobile avec optiques Leica Summilux.', '512Go / 16Go', '5G', 650000.00, 540000.00, 6, 3, 'xiaomi14.png', '12 Mois Xiaomi', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(7, 3, 'Redmi Note 13 Pro+ 5G', 'Redmi Note 13 Pro+ Midnight Black', 'Écran AMOLED 120Hz incurvé, charge ultra rapide 120W et capteur 200MP.', '256Go / 12Go', '5G', 195000.00, 160000.00, 18, 3, 'xiaomi14.png', '12 Mois', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(8, 3, 'Redmi 13C', 'Redmi 13C Midnight Black', 'Smartphone abordable batterie 5000mAh et écran 90Hz 6.74 pouces.', '128Go / 4Go', '4G', 75000.00, 60000.00, 25, 3, 'default_phone.png', '12 Mois', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(9, 4, 'Google Pixel 9 Pro', 'Google Pixel 9 Pro Hazel', 'L\'expérience Android pure sublimée par l\'IA Google Gemini et la puce Tensor G4.', '256Go / 16Go', '5G', 590000.00, 490000.00, 7, 3, 'pixel9.png', '12 Mois Google', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(10, 5, 'Tecno Camon 30 Premier 5G', 'Tecno Camon 30 Premier 5G', 'Spécialiste du portrait photo 50MP avec processeur Dimensity 8200 Ultra.', '512Go / 12Go', '5G', 250000.00, 215000.00, 13, 3, 'default_phone.png', '13 Mois Tecno', '2026-07-29 07:00:40', 'Titane, Noir, Argent'),
(11, 6, 'Infinix Note 50 Pro+ 5G', 'Infinix Note 50 Pro+ 5G', 'Design cuir végétal, recharge sans fil MagCharge et écran 3D AMOLED.', '256Go / 12Go', '5G', 210000.00, 175000.00, 7, 3, '1785435305_6a6b94a9b08ae.jpg', '12 Mois Infinix', '2026-07-29 07:00:40', 'Titane, Noir, Argent');

-- Normalisation des capacités RAM/stockage pour les 11 produits existants.
UPDATE `table_lignes_vente` l
JOIN `table_produits` p ON p.`id_produit` = l.`id_produit`
SET l.`benefice_ligne` = (l.`prix_unitaire` - p.`prix_achat`) * l.`quantite`;

UPDATE `table_produits` SET `id_capacite` = CASE `id_produit`
  WHEN 1 THEN 3
  WHEN 2 THEN 2
  WHEN 3 THEN 1
  WHEN 4 THEN 6
  WHEN 5 THEN 3
  WHEN 6 THEN 7
  WHEN 7 THEN 4
  WHEN 8 THEN 1
  WHEN 9 THEN 5
  WHEN 10 THEN 6
  WHEN 11 THEN 4
  ELSE NULL
END;

ALTER TABLE `table_produits`
  MODIFY `id_capacite` int NOT NULL,
  ADD CONSTRAINT `fk_produits_capacites`
    FOREIGN KEY (`id_capacite`) REFERENCES `table_capacites` (`id_capacite`)
    ON UPDATE CASCADE ON DELETE RESTRICT;

-- --------------------------------------------------------

--
-- Structure de la table `table_reparations`
--

DROP TABLE IF EXISTS `table_reparations`;
CREATE TABLE IF NOT EXISTS `table_reparations` (
  `id_reparation` int NOT NULL AUTO_INCREMENT,
  `code_ticket` varchar(50) NOT NULL,
  `id_client` int DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `appareil` varchar(150) NOT NULL,
  `description_panne` text NOT NULL,
  `devis_est` decimal(12,2) NOT NULL,
  `acompte` decimal(12,2) DEFAULT '0.00',
  `statut` enum('En Atelier','En Cours','Prêt','Livré') DEFAULT 'En Atelier',
  `date_reparation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reparation`),
  UNIQUE KEY `code_ticket` (`code_ticket`),
  KEY `id_client` (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_reparations`
--

INSERT INTO `table_reparations` (`id_reparation`, `code_ticket`, `id_client`, `appareil`, `description_panne`, `devis_est`, `acompte`, `statut`, `date_reparation`) VALUES
(1, 'REP-2026-001', 3, 'iPhone 14 Pro 128Go', 'Écran totalement brisé suite à une chute. Remplacement nécessaire du bloc écran OLED origine.', 120000.00, 60000.00, 'En Cours', '2026-07-29 07:00:40'),
(2, 'REP-2026-002', 4, 'Samsung Galaxy A52', 'Connecteur de charge Type-C dessoudé + batterie gonflée à remplacer.', 35000.00, 35000.00, 'En Atelier', '2026-07-27 07:00:40'),
(3, 'REP-2026-003', 6, 'Xiaomi Redmi Note 12', 'Caméra arrière floue suite à une infiltration d\'eau douce. Nettoyage désoxydation complet.', 25000.00, 10000.00, 'En Atelier', '2026-07-29 07:00:40'),
(4, 'SAV-2026-539', 1, 'Sit nemo dolore volu', 'Repudiandae deleniti', 45.00, 53.00, 'En Atelier', '2026-07-29 07:20:17'),
(5, 'SAV-2026-535', 3, 'APPLE', 'Son gaté', 5000.00, 1000.00, 'En Atelier', '2026-08-01 16:37:50');

-- --------------------------------------------------------

--
-- Structure de la table `table_series`
--

DROP TABLE IF EXISTS `table_series`;
CREATE TABLE IF NOT EXISTS `table_series` (
  `id_serie` int NOT NULL AUTO_INCREMENT,
  `id_marque` int NOT NULL,
  `nom_serie` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id_serie`),
  KEY `id_marque` (`id_marque`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_series`
--

INSERT INTO `table_series` (`id_serie`, `id_marque`, `nom_serie`, `description`) VALUES
(1, 1, 'Xiaomi High-End', NULL),
(2, 1, 'Série Note 15', NULL),
(3, 1, 'Série Note 14', NULL),
(4, 1, 'Série Note 13', NULL),
(5, 2, 'iPhone 16 Series', NULL),
(6, 2, 'iPhone 15 Series', NULL),
(7, 3, 'Galaxy S Series', NULL),
(8, 4, 'Pixel 9 Series', NULL);

-- Correction des associations marque/série :
-- Apple = 1, Samsung = 2, Xiaomi = 3, Google Pixel = 4.
UPDATE `table_series` SET `id_marque` = 3 WHERE `id_serie` IN (1, 2, 3, 4);
UPDATE `table_series` SET `id_marque` = 1 WHERE `id_serie` IN (5, 6);
UPDATE `table_series` SET `id_marque` = 2 WHERE `id_serie` = 7;
UPDATE `table_series` SET `id_marque` = 4 WHERE `id_serie` = 8;


-- --------------------------------------------------------

--
-- Structure de la table `table_stocks`
--

DROP TABLE IF EXISTS `table_stocks`;
CREATE TABLE IF NOT EXISTS `table_stocks` (
  `id_stock` int NOT NULL AUTO_INCREMENT,
  `id_produit` int NOT NULL,
  `quantite_initiale` int DEFAULT '0',
  `quantite_actuelle` int DEFAULT '0',
  `seuil_alerte` int DEFAULT '3',
  `emplacement` varchar(100) DEFAULT 'Boutique Lomé Centre',
  PRIMARY KEY (`id_stock`),
  KEY `id_produit` (`id_produit`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_stocks`
--

INSERT INTO `table_stocks` (`id_stock`, `id_produit`, `quantite_initiale`, `quantite_actuelle`, `seuil_alerte`, `emplacement`) VALUES
(1, 1, 15, 12, 3, 'Boutique Lomé Centre'),
(2, 2, 10, 8, 3, 'Boutique Lomé Centre'),
(3, 3, 20, 18, 3, 'Boutique Lomé Centre'),
(4, 4, 25, 22, 3, 'Boutique Lomé Centre'),
(5, 5, 10, 2, 3, 'Boutique Lomé Centre'),
(6, 6, 30, 28, 3, 'Boutique Lomé Centre'),
(7, 7, 50, 45, 3, 'Boutique Lomé Centre'),
(8, 8, 40, 38, 3, 'Boutique Lomé Centre'),
(9, 9, 35, 31, 3, 'Boutique Lomé Centre'),
(10, 10, 5, 5, 3, 'Boutique Lomé Centre');

-- --------------------------------------------------------

--
-- Structure de la table `table_trocs`
--

DROP TABLE IF EXISTS `table_trocs`;
CREATE TABLE IF NOT EXISTS `table_trocs` (
  `id_troc` int NOT NULL AUTO_INCREMENT,
  `code_troc` varchar(50) NOT NULL,
  `id_client` int DEFAULT NULL,
  `id_user` int DEFAULT NULL,
  `appareil_repris` varchar(150) NOT NULL,
  `valeur_reprise` decimal(12,2) NOT NULL,
  `id_produit_pris` int NOT NULL,
  `prix_vente_pris` decimal(12,2) NOT NULL,
  `soulte_financiere` decimal(12,2) NOT NULL DEFAULT '0.00',
  `soulte_payee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `date_troc` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_troc`),
  UNIQUE KEY `code_troc` (`code_troc`),
  KEY `id_client` (`id_client`),
  KEY `id_produit_pris` (`id_produit_pris`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_trocs`
--

INSERT INTO `table_trocs` (`id_troc`, `code_troc`, `id_client`, `appareil_repris`, `valeur_reprise`, `id_produit_pris`, `prix_vente_pris`, `soulte_payee`, `date_troc`) VALUES
(1, 'TR-2026-001', 2, 'iPhone 13 Pro 128Go (Occasion B+)', 280000.00, 1, 980000.00, 700000.00, '2026-07-28 07:00:40'),
(2, 'TR-2026-002', 5, 'Samsung Galaxy S21 Ultra 256Go', 180000.00, 4, 790000.00, 610000.00, '2026-07-29 07:00:40'),
(3, 'TRC-2026-901', NULL, 'iPhone XLX', 180000.00, 3, 0.00, 0.00, '2026-08-01 16:37:08');

UPDATE `table_trocs` SET `soulte_financiere` = `soulte_payee`;

-- --------------------------------------------------------

--
-- Structure de la table `table_utilisateurs`
--

DROP TABLE IF EXISTS `table_utilisateurs`;
CREATE TABLE IF NOT EXISTS `table_utilisateurs` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `identifiant` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `mot_de_passe_hash` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Vendeur','Stockiste') NOT NULL DEFAULT 'Vendeur',
  `statut` enum('Actif','Inactif') DEFAULT 'Actif',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `guide_complete` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `identifiant` (`identifiant`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_utilisateurs`
--

INSERT INTO `table_utilisateurs` (`id_user`, `nom`, `identifiant`, `mot_de_passe`, `mot_de_passe_hash`, `role`, `statut`, `date_creation`, `guide_complete`) VALUES
(1, 'Direction Générale (Admin)', 'admin', 'admin2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Admin', 'Actif', '2026-07-29 07:00:40', 1),
(2, 'Caissier Principal', 'vendeur', 'vendeur2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Vendeur', 'Actif', '2026-07-29 07:00:40', 0),
(3, 'Vendeur Adjoint', 'vendeur2', 'vendeur2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Vendeur', 'Actif', '2026-07-29 07:00:40', 0),
(4, 'Gestionnaire Stock', 'stockiste', 'stock2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Stockiste', 'Actif', '2026-07-29 07:00:40', 0),
(5, 'Josias', 'jo', '123456789', NULL, 'Vendeur', 'Actif', '2026-08-19 22:48:11', 1);

-- --------------------------------------------------------

--
-- Structure de la table `table_ventes`
--

DROP TABLE IF EXISTS `table_ventes`;
CREATE TABLE IF NOT EXISTS `table_ventes` (
  `id_vente` int NOT NULL AUTO_INCREMENT,
  `code_facture` varchar(50) NOT NULL,
  `id_client` int DEFAULT NULL,
  `id_user` int NOT NULL,
  `total_ht` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remise` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_ttc` decimal(12,2) NOT NULL,
  `mode_paiement` enum('Espèces','Flooz','T-Money','Carte Bancaire') DEFAULT 'Espèces',
  `date_vente` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_vente`),
  UNIQUE KEY `code_facture` (`code_facture`),
  KEY `id_client` (`id_client`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `table_ventes`
--

INSERT INTO `table_ventes` (`id_vente`, `code_facture`, `id_client`, `id_user`, `total_ttc`, `mode_paiement`, `date_vente`) VALUES
(1, 'FAC-2026-001', 1, 2, 980000.00, 'Carte Bancaire', '2026-07-29 07:00:40'),
(2, 'FAC-2026-002', 2, 2, 790000.00, 'T-Money', '2026-07-29 07:00:40'),
(3, 'FAC-2026-003', 4, 3, 245000.00, 'Flooz', '2026-07-29 07:00:40'),
(4, 'FAC-2026-004', 6, 2, 650000.00, 'Espèces', '2026-07-28 07:00:40'),
(5, 'FAC-2026-9066', NULL, 1, 680000.00, 'Espèces', '2026-07-29 07:16:04'),
(6, 'FAC-2026-5336', NULL, 1, 245000.00, 'Espèces', '2026-08-01 15:54:42'),
(7, 'FAC-2026-2034', NULL, 1, 210000.00, 'Espèces', '2026-08-01 16:34:57'),
(8, 'FAC-2026-1733', NULL, 1, 1010000.00, 'Espèces', '2026-08-06 15:28:06'),
(9, 'FAC-2026-7974', NULL, 1, 1040000.00, 'Espèces', '2026-08-06 15:31:23'),
(10, 'FAC-2026-1915', NULL, 1, 1480000.00, 'Espèces', '2026-08-06 16:24:02');

UPDATE `table_ventes` SET `total_ht` = `total_ttc`;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `table_imei`
--
ALTER TABLE `table_imei`
  ADD CONSTRAINT `table_imei_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `table_produits` (`id_produit`);

--
-- Contraintes pour la table `table_lignes_vente`
--
ALTER TABLE `table_lignes_vente`
  ADD CONSTRAINT `table_lignes_vente_ibfk_1` FOREIGN KEY (`id_vente`) REFERENCES `table_ventes` (`id_vente`) ON DELETE CASCADE,
  ADD CONSTRAINT `table_lignes_vente_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `table_produits` (`id_produit`);

--
-- Contraintes pour la table `table_paiements`
--
ALTER TABLE `table_paiements`
  ADD CONSTRAINT `table_paiements_ibfk_1` FOREIGN KEY (`id_vente`) REFERENCES `table_ventes` (`id_vente`) ON DELETE CASCADE;

--
-- Contraintes pour la table `table_prix`
--
ALTER TABLE `table_prix`
  ADD CONSTRAINT `table_prix_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `table_produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `table_produits`
--
ALTER TABLE `table_produits`
  ADD CONSTRAINT `table_produits_ibfk_1` FOREIGN KEY (`id_marque`) REFERENCES `table_marques` (`id_marque`) ON DELETE CASCADE;

--
-- Contraintes pour la table `table_reparations`
--
ALTER TABLE `table_reparations`
  ADD CONSTRAINT `table_reparations_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `table_clients` (`id_client`);

--
-- Contraintes pour la table `table_series`
--
ALTER TABLE `table_series`
  ADD CONSTRAINT `table_series_ibfk_1` FOREIGN KEY (`id_marque`) REFERENCES `table_marques` (`id_marque`) ON DELETE CASCADE;

--
-- Contraintes pour la table `table_stocks`
--
ALTER TABLE `table_stocks`
  ADD CONSTRAINT `table_stocks_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `table_produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `table_trocs`
--
ALTER TABLE `table_trocs`
  ADD CONSTRAINT `table_trocs_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `table_clients` (`id_client`),
  ADD CONSTRAINT `table_trocs_ibfk_2` FOREIGN KEY (`id_produit_pris`) REFERENCES `table_produits` (`id_produit`);

--
-- Contraintes pour la table `table_ventes`
--
ALTER TABLE `table_ventes`
  ADD CONSTRAINT `table_ventes_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `table_clients` (`id_client`) ON DELETE SET NULL,
  ADD CONSTRAINT `table_ventes_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `table_utilisateurs` (`id_user`);
COMMIT;


-- ========================================================
-- CONTROLES DE COHERENCE A EXECUTER APRES IMPORTATION
-- Ces requêtes signalent les écarts sans modifier les données.
-- ========================================================

-- 1) Ecarts entre le stock du catalogue et le stock détaillé.
SELECT p.id_produit, p.nom_produit,
       p.quantite_stock AS stock_catalogue,
       s.quantite_actuelle AS stock_detaille
FROM table_produits p
LEFT JOIN table_stocks s ON s.id_produit = p.id_produit
WHERE s.id_stock IS NULL
   OR p.quantite_stock <> s.quantite_actuelle;

-- 2) Ecarts entre les prix du catalogue et la table des prix.
SELECT p.id_produit, p.nom_produit,
       p.prix_achat AS achat_catalogue,
       pr.prix_achat AS achat_table_prix,
       p.prix_vente AS vente_catalogue,
       pr.prix_vente_moyen AS vente_table_prix
FROM table_produits p
LEFT JOIN table_prix pr ON pr.id_produit = p.id_produit
WHERE pr.id_prix IS NULL
   OR p.prix_achat <> pr.prix_achat
   OR p.prix_vente <> pr.prix_vente_moyen;

-- 3) Réparations dont l'acompte dépasse le devis.
SELECT id_reparation, code_ticket, devis_est, acompte
FROM table_reparations
WHERE acompte > devis_est;

-- 4) IMEI avec un numéro secondaire identique au numéro principal.
SELECT id_imei, id_produit, imei1, imei2
FROM table_imei
WHERE imei2 IS NOT NULL AND imei1 = imei2;

-- 5) Produits sans prix renseigné dans table_prix.
SELECT p.id_produit, p.nom_produit
FROM table_produits p
LEFT JOIN table_prix pr ON pr.id_produit = p.id_produit
WHERE pr.id_prix IS NULL;

-- 6) Ventes dont le total diffère de la somme des lignes.
SELECT v.id_vente, v.code_facture, v.total_ttc,
       COALESCE(SUM(l.quantite * l.prix_unitaire), 0) AS total_lignes
FROM table_ventes v
LEFT JOIN table_lignes_vente l ON l.id_vente = v.id_vente
GROUP BY v.id_vente, v.code_facture, v.total_ttc
HAVING v.total_ttc <> COALESCE(SUM(l.quantite * l.prix_unitaire), 0);



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

SET FOREIGN_KEY_CHECKS = 1;
