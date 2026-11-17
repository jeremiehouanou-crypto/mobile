-- ==============================================================================
-- BASE DE DONNÉES MYSQL: MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
-- Script Complet de Réinitialisation avec Données Réalistes
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `mobilehome_sgc_corrige` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mobilehome_sgc_corrige`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `table_reparations`;
DROP TABLE IF EXISTS `table_trocs`;
DROP TABLE IF EXISTS `table_lignes_vente`;
DROP TABLE IF EXISTS `table_ventes`;
DROP TABLE IF EXISTS `table_fournisseurs`;
DROP TABLE IF EXISTS `table_clients`;
DROP TABLE IF EXISTS `table_produits`;
DROP TABLE IF EXISTS `table_marques`;
DROP TABLE IF EXISTS `table_utilisateurs`;
DROP TABLE IF EXISTS `table_entreprise`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Table Entreprise
CREATE TABLE `table_entreprise` (
    `id_entreprise` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(150) NOT NULL DEFAULT 'JY SHOP STORE',
    `slogan` VARCHAR(255) DEFAULT 'PC HP - 8e a 13e generation',
    `adresse` VARCHAR(255) DEFAULT 'À compléter',
    `tel1` VARCHAR(50) DEFAULT '+229 90 38 90 62',
    `tel2` VARCHAR(50) DEFAULT '',
    `whatsapp` VARCHAR(50) DEFAULT '+229 90 38 90 62',
    `email` VARCHAR(150) DEFAULT '',
    `logo_url` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table Utilisateurs (Authentification & Rôles)
CREATE TABLE `table_utilisateurs` (
    `id_user` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `identifiant` VARCHAR(50) UNIQUE NOT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `mot_de_passe_hash` VARCHAR(255) DEFAULT NULL,
    `role` ENUM('Admin', 'Vendeur', 'Stockiste') NOT NULL DEFAULT 'Vendeur',
    `statut` ENUM('Actif', 'Inactif') DEFAULT 'Actif',
    `guide_complete` TINYINT(1) DEFAULT 0,
    `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table Marques
CREATE TABLE `table_marques` (
    `id_marque` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_marque` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `table_marques` (`nom_marque`) VALUES ('HP');

CREATE TABLE `table_capacites` (
    `id_capacite` INT AUTO_INCREMENT PRIMARY KEY,
    `stockage_go` INT NOT NULL,
    `ram_go` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table Produits (Catalogue avec Photos)
CREATE TABLE `table_produits` (
    `id_produit` INT AUTO_INCREMENT PRIMARY KEY,
    `id_marque` INT NOT NULL,
    `id_capacite` INT DEFAULT NULL,
    `nom_produit` VARCHAR(150) NOT NULL,
    `modele_exact` VARCHAR(150) DEFAULT NULL,
    `description` TEXT,
    `stockage_ram` VARCHAR(50) DEFAULT '8 Go RAM / 256 Go SSD',
    `reseau` VARCHAR(20) DEFAULT 'PC',
    `generation` TINYINT UNSIGNED DEFAULT NULL,
    `processeur` VARCHAR(120) DEFAULT NULL,
    `carte_graphique` VARCHAR(120) DEFAULT NULL,
    `type_pc` VARCHAR(40) NOT NULL DEFAULT 'Bureautique',
    `prix_vente` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `prix_achat` DECIMAL(12,2) DEFAULT 0.00,
    `quantite_stock` INT DEFAULT 0,
    `seuil_alerte` INT DEFAULT 3,
    `image_url` VARCHAR(255) DEFAULT NULL,
    `garantie` VARCHAR(50) DEFAULT '12 Mois',
    `date_ajout` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_marque`) REFERENCES `table_marques`(`id_marque`) ON DELETE CASCADE,
    FOREIGN KEY (`id_capacite`) REFERENCES `table_capacites`(`id_capacite`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table Clients
CREATE TABLE `table_clients` (
    `id_client` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) DEFAULT NULL,
    `telephone` VARCHAR(30) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `localisation` VARCHAR(255) DEFAULT 'Lomé',
    `cumul_achats` DECIMAL(12,2) DEFAULT 0.00,
    `statut_fidelite` VARCHAR(50) DEFAULT 'Standard',
    `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table Fournisseurs
CREATE TABLE `table_fournisseurs` (
    `id_fournisseur` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_societe` VARCHAR(150) NOT NULL,
    `nom_contact` VARCHAR(100) NOT NULL,
    `telephone` VARCHAR(50) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `produits_fournis` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Table Ventes
CREATE TABLE `table_ventes` (
    `id_vente` INT AUTO_INCREMENT PRIMARY KEY,
    `code_facture` VARCHAR(50) NOT NULL UNIQUE,
    `id_client` INT DEFAULT NULL,
    `id_user` INT NOT NULL,
    `total_ttc` DECIMAL(12,2) NOT NULL,
    `mode_paiement` ENUM('Espèces', 'Flooz', 'T-Money', 'Carte Bancaire') DEFAULT 'Espèces',
    `date_vente` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_client`) REFERENCES `table_clients`(`id_client`) ON DELETE SET NULL,
    FOREIGN KEY (`id_user`) REFERENCES `table_utilisateurs`(`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Table Lignes de Vente
CREATE TABLE `table_lignes_vente` (
    `id_ligne` INT AUTO_INCREMENT PRIMARY KEY,
    `id_vente` INT NOT NULL,
    `id_produit` INT NOT NULL,
    `quantite` INT NOT NULL DEFAULT 1,
    `prix_unitaire` DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (`id_vente`) REFERENCES `table_ventes`(`id_vente`) ON DELETE CASCADE,
    FOREIGN KEY (`id_produit`) REFERENCES `table_produits`(`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Table Trocs
CREATE TABLE `table_trocs` (
    `id_troc` INT AUTO_INCREMENT PRIMARY KEY,
    `code_troc` VARCHAR(50) NOT NULL UNIQUE,
    `id_client` INT DEFAULT NULL,
    `appareil_repris` VARCHAR(150) NOT NULL,
    `valeur_reprise` DECIMAL(12,2) NOT NULL,
    `id_produit_pris` INT NOT NULL,
    `prix_vente_pris` DECIMAL(12,2) NOT NULL,
    `soulte_payee` DECIMAL(12,2) NOT NULL,
    `date_troc` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_client`) REFERENCES `table_clients`(`id_client`),
    FOREIGN KEY (`id_produit_pris`) REFERENCES `table_produits`(`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Table Réparations SAV
CREATE TABLE `table_reparations` (
    `id_reparation` INT AUTO_INCREMENT PRIMARY KEY,
    `code_ticket` VARCHAR(50) NOT NULL UNIQUE,
    `id_client` INT DEFAULT NULL,
    `appareil` VARCHAR(150) NOT NULL,
    `description_panne` TEXT NOT NULL,
    `devis_est` DECIMAL(12,2) NOT NULL,
    `acompte` DECIMAL(12,2) DEFAULT 0.00,
    `statut` ENUM('En Atelier', 'En Cours', 'Prêt', 'Livré') DEFAULT 'En Atelier',
    `date_reparation` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_client`) REFERENCES `table_clients`(`id_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ==============================================================================
-- DONNÉES DE SEEDING RÉALISTES (MOBILE HOME TOGO)
-- ==============================================================================

-- Entreprise
INSERT INTO `table_entreprise` (`nom`, `slogan`, `adresse`, `tel1`, `tel2`, `whatsapp`, `email`, `logo_url`) VALUES
('JY SHOP STORE', 'PC HP - 8e a 13e generation', 'À compléter', '+229 90 38 90 62', '', '+229 90 38 90 62', '', '');

-- Utilisateurs
INSERT INTO `table_utilisateurs` (`nom`, `identifiant`, `mot_de_passe`, `mot_de_passe_hash`, `role`, `statut`) VALUES
('Direction Générale (Admin)', 'admin', 'admin2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Admin', 'Actif'),
('Caissier Principal', 'vendeur', 'vendeur2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Vendeur', 'Actif'),
('Vendeur Adjoint', 'vendeur2', 'vendeur2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Vendeur', 'Actif'),
('Gestionnaire Stock', 'stockiste', 'stock2026', '$2y$10$w8T0M0M.6fM5Rk3oO1eB1.yGzM1w/1z.xXzZzZzZzZzZzZzZzZzZz', 'Stockiste', 'Actif');

-- Marques
INSERT INTO `table_marques` (`id_marque`, `nom_marque`) VALUES
(1, 'Apple'),
(2, 'Samsung'),
(3, 'Xiaomi'),
(4, 'Google Pixel'),
(5, 'Tecno'),
(6, 'Infinix');

INSERT IGNORE INTO `table_marques` (`nom_marque`) VALUES ('HP');

-- Produits (avec visuels réels)
INSERT INTO `table_produits` (`id_marque`, `nom_produit`, `modele_exact`, `description`, `stockage_ram`, `reseau`, `prix_vente`, `prix_achat`, `quantite_stock`, `seuil_alerte`, `image_url`, `garantie`) VALUES
(1, 'iPhone 16 Pro Max', 'iPhone 16 Pro Max Titanium', 'Flagship Apple avec puce A18 Pro et écran Super Retina XDR 6.9 pouces.', '256Go / 8Go', '5G', 980000.00, 850000.00, 12, 3, 'iphone16.png', '12 Mois Apple'),
(1, 'iPhone 15 Pro', 'iPhone 15 Pro Titane Naturel', 'Smartphone Apple premium avec puce A17 Pro et châssis titane.', '128Go / 8Go', '5G', 680000.00, 590000.00, 8, 3, 'iphone16.png', '12 Mois Apple'),
(1, 'iPhone 13', 'iPhone 13 Minuit', 'iPhone haute performance, double capteur photo 12MP et excellente autonomie.', '128Go / 4Go', '5G', 420000.00, 350000.00, 2, 3, 'default_phone.png', '12 Mois'),
(2, 'Samsung Galaxy S24 Ultra', 'Galaxy S24 Ultra Titanium Gray', 'Le roi Android avec S-Pen intégré, Snapdragon 8 Gen 3 et capteur 200MP.', '512Go / 12Go', '5G', 790000.00, 680000.00, 15, 3, 's24ultra.png', '24 Mois Samsung'),
(2, 'Samsung Galaxy A55 5G', 'Galaxy A55 5G Awesome Navy', 'Best-seller milieu de gamme avec finition verre premium et étanchéité IP67.', '256Go / 8Go', '5G', 245000.00, 200000.00, 20, 3, 's24ultra.png', '12 Mois Samsung'),
(3, 'Xiaomi 14 Ultra', 'Xiaomi 14 Ultra Black Leica', 'Monstre de la photographie mobile avec optiques Leica Summilux.', '512Go / 16Go', '5G', 650000.00, 540000.00, 6, 3, 'xiaomi14.png', '12 Mois Xiaomi'),
(3, 'Redmi Note 13 Pro+ 5G', 'Redmi Note 13 Pro+ Midnight Black', 'Écran AMOLED 120Hz incurvé, charge ultra rapide 120W et capteur 200MP.', '256Go / 12Go', '5G', 195000.00, 160000.00, 18, 3, 'xiaomi14.png', '12 Mois'),
(3, 'Redmi 13C', 'Redmi 13C Midnight Black', 'Smartphone abordable batterie 5000mAh et écran 90Hz 6.74 pouces.', '128Go / 4Go', '4G', 75000.00, 60000.00, 25, 3, 'default_phone.png', '12 Mois'),
(4, 'Google Pixel 9 Pro', 'Google Pixel 9 Pro Hazel', 'L\'expérience Android pure sublimée par l\'IA Google Gemini et la puce Tensor G4.', '256Go / 16Go', '5G', 590000.00, 490000.00, 9, 3, 'pixel9.png', '12 Mois Google'),
(5, 'Tecno Camon 30 Premier 5G', 'Camon 30 Premier Silver', 'Spécialiste du portrait photo 50MP avec processeur Dimensity 8200 Ultra.', '512Go / 12Go', '5G', 260000.00, 215000.00, 14, 3, 'default_phone.png', '13 Mois Tecno'),
(6, 'Infinix Note 40 Pro+ 5G', 'Note 40 Pro+ Vintage Green', 'Design cuir végétal, recharge sans fil MagCharge et écran 3D AMOLED.', '256Go / 12Go', '5G', 210000.00, 175000.00, 11, 3, 'default_phone.png', '12 Mois Infinix');

-- Clients
INSERT INTO `table_clients` (`nom`, `prenom`, `telephone`, `email`, `localisation`, `cumul_achats`, `statut_fidelite`) VALUES
('Bamba', 'Fatoumata', '+228 90 12 34 56', 'fatoumata.bamba@gmail.com', 'Lomé Tokoin', 1770000.00, 'VIP Gold'),
('Lawson', 'Koffi', '+228 91 00 11 22', 'koffi.lawson@hotmail.com', 'Lomé Adidogomé', 980000.00, 'VIP Silver'),
('Diallo', 'Moussa', '+228 99 88 77 66', 'moussa.diallo@yahoo.fr', 'Lomé Agbalépédogan', 455000.00, 'Standard'),
('Dogbé', 'Afiwa', '+228 92 33 44 55', 'afiwa.dogbe@gmail.com', 'Lomé Bè', 245000.00, 'Standard'),
('Amouzou', 'Kodjo', '+228 93 77 88 99', 'kodjo.amouzou@yahoo.fr', 'Lomé Totsi', 650000.00, 'Standard'),
('Johnson', 'Eya', '+228 96 11 22 33', 'eya.johnson@outlook.com', 'Lomé Kégué', 1210000.00, 'VIP Silver');

-- Fournisseurs
INSERT INTO `table_fournisseurs` (`nom_societe`, `nom_contact`, `telephone`, `email`, `produits_fournis`) VALUES
('Dubai Phone Wholesale LLC', 'Mr. Tariq Al-Mansoor', '+971 50 123 4567', 'sales@dubaiphone.ae', 'iPhones neufs certifiés & accessoires Apple d\'origine'),
('HK Telecom Trading Co.', 'Mme. Li Zhang', '+852 9876 5432', 'zhang@hktelecom.hk', 'Smartphones Xiaomi, Redmi et Poco officiels'),
('Samsung Gulf Electronics FZE', 'Mr. Karim Haddad', '+971 4 888 9900', 'info@samsunggulf.ae', 'Gamme complète Samsung Galaxy S & A'),
('Transsion Official Togo (Tecno/Infinix)', 'Représentant Togo', '+228 90 00 11 22', 'togo@transsion.com', 'Smartphones Tecno Camon, Infinix Note & Itel');

-- Ventes
INSERT INTO `table_ventes` (`code_facture`, `id_client`, `id_user`, `total_ttc`, `mode_paiement`, `date_vente`) VALUES
('FAC-2026-001', 1, 2, 980000.00, 'Carte Bancaire', NOW()),
('FAC-2026-002', 2, 2, 790000.00, 'T-Money', NOW()),
('FAC-2026-003', 4, 3, 245000.00, 'Flooz', NOW()),
('FAC-2026-004', 6, 2, 650000.00, 'Espèces', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Lignes de Vente
INSERT INTO `table_lignes_vente` (`id_vente`, `id_produit`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 1, 980000.00),
(2, 4, 1, 790000.00),
(3, 5, 1, 245000.00),
(4, 6, 1, 650000.00);

-- Trocs
INSERT INTO `table_trocs` (`code_troc`, `id_client`, `appareil_repris`, `valeur_reprise`, `id_produit_pris`, `prix_vente_pris`, `soulte_payee`, `date_troc`) VALUES
('TR-2026-001', 2, 'iPhone 13 Pro 128Go (Occasion B+)', 280000.00, 1, 980000.00, 700000.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
('TR-2026-002', 5, 'Samsung Galaxy S21 Ultra 256Go', 180000.00, 4, 790000.00, 610000.00, NOW());

-- Réparations SAV
INSERT INTO `table_reparations` (`code_ticket`, `id_client`, `appareil`, `description_panne`, `devis_est`, `acompte`, `statut`, `date_reparation`) VALUES
('REP-2026-001', 3, 'iPhone 14 Pro 128Go', 'Écran totalement brisé suite à une chute. Remplacement nécessaire du bloc écran OLED origine.', 120000.00, 60000.00, 'En Cours', NOW()),
('REP-2026-002', 4, 'Samsung Galaxy A52', 'Connecteur de charge Type-C dessoudé + batterie gonflée à remplacer.', 35000.00, 35000.00, 'Prêt', DATE_SUB(NOW(), INTERVAL 2 DAY)),
('REP-2026-003', 6, 'Xiaomi Redmi Note 12', 'Caméra arrière floue suite à une infiltration d\'eau douce. Nettoyage désoxydation complet.', 25000.00, 10000.00, 'En Atelier', NOW());
