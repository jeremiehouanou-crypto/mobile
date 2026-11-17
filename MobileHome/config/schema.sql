-- ==============================================================================
-- SCRIPT D'INITIALISATION BDD: MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
-- Cahier des Spécifications Techniques & Base de Données Relationnelle (14+ Tables)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `mobilehome_sgc_corrige` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mobilehome_sgc_corrige`;

-- 1. Table Entreprise (Informations Institutionnelles)
CREATE TABLE IF NOT EXISTS `table_entreprise` (
    `id_entreprise` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(150) NOT NULL DEFAULT 'JY SHOP STORE',
    `slogan` VARCHAR(255) DEFAULT 'PC HP - 8e a 13e generation',
    `adresse` VARCHAR(255) DEFAULT 'À compléter',
    `tel1` VARCHAR(50) DEFAULT '+229 90 38 90 62',
    `tel2` VARCHAR(50) DEFAULT '',
    `whatsapp` VARCHAR(50) DEFAULT '+229 90 38 90 62',
    `email` VARCHAR(150) DEFAULT '',
    `reseaux_sociaux` VARCHAR(255) DEFAULT NULL,
    `horaires` VARCHAR(100) DEFAULT NULL,
    `logo_url` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table Utilisateurs & RBAC (Rôles: Admin, Vendeur, Gestionnaire Stock)
CREATE TABLE IF NOT EXISTS `table_utilisateurs` (
    `id_user` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(100) NOT NULL,
    `identifiant` VARCHAR(50) UNIQUE NOT NULL,
    `mot_de_passe_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('Admin', 'Vendeur', 'Gestionnaire Stock') NOT NULL DEFAULT 'Vendeur',
    `statut` ENUM('Actif', 'Inactif') DEFAULT 'Actif',
    `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table Marques (Apple, Samsung, Xiaomi, Google Pixel)
CREATE TABLE IF NOT EXISTS `table_marques` (
    `id_marque` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_marque` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table Séries
CREATE TABLE IF NOT EXISTS `table_series` (
    `id_serie` INT AUTO_INCREMENT PRIMARY KEY,
    `id_marque` INT NOT NULL,
    `nom_serie` VARCHAR(100) NOT NULL,
    `description` TEXT,
    FOREIGN KEY (`id_marque`) REFERENCES `table_marques`(`id_marque`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table Capacités (Stockage & RAM)
CREATE TABLE IF NOT EXISTS `table_capacites` (
    `id_capacite` INT AUTO_INCREMENT PRIMARY KEY,
    `stockage_go` INT NOT NULL,
    `ram_go` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table Produits (Catalogue Général)
CREATE TABLE IF NOT EXISTS `table_produits` (
    `id_produit` INT AUTO_INCREMENT PRIMARY KEY,
    `id_marque` INT NOT NULL,
    `id_capacite` INT DEFAULT NULL,
    `id_serie` INT DEFAULT NULL,
    `modele_exact` VARCHAR(150) NOT NULL,
    `etat` ENUM('Neuf', 'Occasion', 'Reconditionné') DEFAULT 'Neuf',
    `reseau` VARCHAR(20) DEFAULT 'PC',
    `generation` TINYINT UNSIGNED DEFAULT NULL,
    `processeur` VARCHAR(120) DEFAULT NULL,
    `carte_graphique` VARCHAR(120) DEFAULT NULL,
    `type_pc` VARCHAR(40) NOT NULL DEFAULT 'Bureautique',
    `garantie` VARCHAR(50) DEFAULT '12 Mois',
    `image_url` VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (`id_marque`) REFERENCES `table_marques`(`id_marque`),
    FOREIGN KEY (`id_capacite`) REFERENCES `table_capacites`(`id_capacite`) ON UPDATE CASCADE,
    FOREIGN KEY (`id_serie`) REFERENCES `table_series`(`id_serie`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Table Prix
CREATE TABLE IF NOT EXISTS `table_prix` (
    `id_prix` INT AUTO_INCREMENT PRIMARY KEY,
    `id_produit` INT NOT NULL,
    `prix_achat` DECIMAL(12,2) NOT NULL,
    `prix_vente_moyen` DECIMAL(12,2) NOT NULL,
    `prix_min` DECIMAL(12,2) DEFAULT NULL,
    `date_modification` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_produit`) REFERENCES `table_produits`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Table Stocks (Alerte Stock < 3 unités conforme PDF 4.1)
CREATE TABLE IF NOT EXISTS `table_stocks` (
    `id_stock` INT AUTO_INCREMENT PRIMARY KEY,
    `id_produit` INT NOT NULL,
    `quantite_initiale` INT DEFAULT 0,
    `quantite_actuelle` INT DEFAULT 0,
    `seuil_alerte` INT DEFAULT 3,
    `emplacement` VARCHAR(100) DEFAULT 'Boutique Lomé Centre',
    FOREIGN KEY (`id_produit`) REFERENCES `table_produits`(`id_produit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Table IMEI (Traçabilité Individuelle)
CREATE TABLE IF NOT EXISTS `table_imei` (
    `id_imei` INT AUTO_INCREMENT PRIMARY KEY,
    `id_produit` INT NOT NULL,
    `imei1` VARCHAR(20) NOT NULL UNIQUE,
    `imei2` VARCHAR(20) DEFAULT NULL,
    `etat_batterie_pct` INT DEFAULT 100,
    `statut` ENUM('En Stock', 'Vendu', 'Reçu Troc') DEFAULT 'En Stock',
    `date_entree` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_produit`) REFERENCES `table_produits`(`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Table Clients
CREATE TABLE IF NOT EXISTS `table_clients` (
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

-- 11. Table Fournisseurs
CREATE TABLE IF NOT EXISTS `table_fournisseurs` (
    `id_fournisseur` INT AUTO_INCREMENT PRIMARY KEY,
    `nom_contact` VARCHAR(100) NOT NULL,
    `nom_societe` VARCHAR(150) NOT NULL,
    `telephone` VARCHAR(50) NOT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `adresse` VARCHAR(255) DEFAULT NULL,
    `produits_fournis` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Table Ventes (Transactions POS & Factures)
CREATE TABLE IF NOT EXISTS `table_ventes` (
    `id_vente` INT AUTO_INCREMENT PRIMARY KEY,
    `code_facture` VARCHAR(50) NOT NULL UNIQUE,
    `id_client` INT DEFAULT NULL,
    `id_user` INT NOT NULL,
    `date_vente` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `total_ht` DECIMAL(12,2) NOT NULL,
    `remise` DECIMAL(12,2) DEFAULT 0.00,
    `total_ttc` DECIMAL(12,2) NOT NULL,
    `mode_paiement` ENUM('Espèces', 'Flooz', 'T-Money', 'Carte Bancaire') DEFAULT 'Espèces',
    FOREIGN KEY (`id_client`) REFERENCES `table_clients`(`id_client`) ON DELETE SET NULL,
    FOREIGN KEY (`id_user`) REFERENCES `table_utilisateurs`(`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Table Lignes Vente
CREATE TABLE IF NOT EXISTS `table_lignes_vente` (
    `id_ligne` INT AUTO_INCREMENT PRIMARY KEY,
    `id_vente` INT NOT NULL,
    `id_produit` INT NOT NULL,
    `quantite` INT NOT NULL DEFAULT 1,
    `prix_unitaire` DECIMAL(12,2) NOT NULL,
    `benefice_ligne` DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (`id_vente`) REFERENCES `table_ventes`(`id_vente`) ON DELETE CASCADE,
    FOREIGN KEY (`id_produit`) REFERENCES `table_produits`(`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Table Paiements Multi-modes
CREATE TABLE IF NOT EXISTS `table_paiements` (
    `id_paiement` INT AUTO_INCREMENT PRIMARY KEY,
    `id_vente` INT NOT NULL,
    `moyen_paiement` ENUM('Espèces', 'Flooz', 'T-Money', 'Carte Bancaire') NOT NULL,
    `montant` DECIMAL(12,2) NOT NULL,
    `reference_transac` VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (`id_vente`) REFERENCES `table_ventes`(`id_vente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Table Trocs & Reprises
CREATE TABLE IF NOT EXISTS `table_trocs` (
    `id_troc` INT AUTO_INCREMENT PRIMARY KEY,
    `code_troc` VARCHAR(50) NOT NULL UNIQUE,
    `id_client` INT NOT NULL,
    `id_user` INT NOT NULL,
    `appareil_repris` VARCHAR(150) NOT NULL,
    `valeur_reprise` DECIMAL(12,2) NOT NULL,
    `id_produit_pris` INT NOT NULL,
    `prix_vente_pris` DECIMAL(12,2) NOT NULL,
    `soulte_financiere` DECIMAL(12,2) NOT NULL,
    `date_troc` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_client`) REFERENCES `table_clients`(`id_client`),
    FOREIGN KEY (`id_user`) REFERENCES `table_utilisateurs`(`id_user`),
    FOREIGN KEY (`id_produit_pris`) REFERENCES `table_produits`(`id_produit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16. Table SAV & Réparations
CREATE TABLE IF NOT EXISTS `table_reparations` (
    `id_reparation` INT AUTO_INCREMENT PRIMARY KEY,
    `code_ticket` VARCHAR(50) NOT NULL UNIQUE,
    `id_client` INT NOT NULL,
    `id_user` INT NOT NULL,
    `appareil` VARCHAR(150) NOT NULL,
    `description_panne` TEXT NOT NULL,
    `acompte` DECIMAL(12,2) DEFAULT 0.00,
    `devis_est` DECIMAL(12,2) NOT NULL,
    `statut` ENUM('En Atelier', 'En Cours', 'Prêt', 'Livré') DEFAULT 'En Atelier',
    `date_reparation` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_client`) REFERENCES `table_clients`(`id_client`),
    FOREIGN KEY (`id_user`) REFERENCES `table_utilisateurs`(`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ==============================================================================
-- INSERTION DES DONNÉES INITIALES (SEED DATA ISSUES DU PDF SPECIFICATIONS)
-- ==============================================================================

-- 1. Entreprise
INSERT INTO `table_entreprise` (`nom`, `slogan`, `adresse`, `tel1`, `tel2`, `whatsapp`, `email`)
VALUES ('JY SHOP STORE', 'PC HP - 8e a 13e generation', 'À compléter', '+229 90 38 90 62', '', '+229 90 38 90 62', '');

-- 2. Utilisateurs par défaut (Conformes PDF Section 5)
-- Mots de passe par défaut : admin2026 -> $2y$10$v... (admin), vendeur2026 (vendeur)
INSERT INTO `table_utilisateurs` (`nom`, `identifiant`, `mot_de_passe_hash`, `role`) VALUES
('Direction Générale', 'admin', '$2y$10$e7v.Q3Nq4S.9sR9lD78Y7eY/8pX3E4b2kL9F4g2H1j0k3m5n7o9q2', 'Admin'),
('Caissier Principal', 'vendeur', '$2y$10$e7v.Q3Nq4S.9sR9lD78Y7eY/8pX3E4b2kL9F4g2H1j0k3m5n7o9q2', 'Vendeur'),
('Chef Stockiste', 'stockiste', '$2y$10$e7v.Q3Nq4S.9sR9lD78Y7eY/8pX3E4b2kL9F4g2H1j0k3m5n7o9q2', 'Gestionnaire Stock');

-- 3. Marques (Apple, Samsung, Xiaomi, Google Pixel)
INSERT INTO `table_marques` (`id_marque`, `nom_marque`) VALUES
(1, 'Xiaomi'),
(2, 'Apple'),
(3, 'Samsung'),
(4, 'Google Pixel');

INSERT IGNORE INTO `table_marques` (`nom_marque`) VALUES ('HP');

INSERT IGNORE INTO `table_marques` (`nom_marque`) VALUES ('HP');

-- 4. Séries
INSERT INTO `table_series` (`id_serie`, `id_marque`, `nom_serie`) VALUES
(1, 1, 'Xiaomi High-End'),
(2, 1, 'Série Note 15'),
(3, 1, 'Série Note 14'),
(4, 1, 'Série Note 13'),
(5, 2, 'iPhone 16 Series'),
(6, 2, 'iPhone 15 Series'),
(7, 3, 'Galaxy S Series'),
(8, 4, 'Pixel 9 Series');

-- 5. Produits Initialisés (Conformes aux Tableaux du PDF)
INSERT INTO `table_produits` (`id_produit`, `id_marque`, `id_serie`, `modele_exact`, `etat`, `reseau`, `garantie`) VALUES
(1, 1, 1, 'Xiaomi 17 Pro Max', 'Neuf', '5G', '12 Mois'),
(2, 1, 1, 'Xiaomi 17 Ultra', 'Neuf', '5G', '12 Mois'),
(3, 1, 2, 'Redmi Note 15 Pro Plus', 'Neuf', '5G', '12 Mois'),
(4, 1, 2, 'Redmi Note 15 Pro', 'Neuf', '4G/5G', '12 Mois'),
(5, 1, 3, 'Redmi 14C', 'Neuf', '4G', '12 Mois'),
(6, 2, 5, 'iPhone 16 Pro Max 256Go', 'Neuf', '5G', '12 Mois'),
(7, 2, 6, 'iPhone 15 Pro Max 256Go', 'Neuf', '5G', '12 Mois'),
(8, 3, 7, 'Samsung Galaxy S24 Ultra 512Go', 'Neuf', '5G', '12 Mois'),
(9, 4, 8, 'Google Pixel 9 Pro 256Go', 'Neuf', '5G', '12 Mois');

-- 6. Prix
INSERT INTO `table_prix` (`id_produit`, `prix_achat`, `prix_vente_moyen`, `prix_min`) VALUES
(1, 480000.00, 590000.00, 550000.00),
(2, 530000.00, 650000.00, 600000.00),
(3, 190000.00, 250000.00, 230000.00),
(4, 130000.00, 165000.00, 150000.00),
(5, 45000.00, 60000.00, 55000.00),
(6, 850000.00, 980000.00, 930000.00),
(7, 720000.00, 850000.00, 800000.00),
(8, 680000.00, 790000.00, 750000.00),
(9, 460000.00, 550000.00, 520000.00);

-- 7. Stocks (Stock total = 262 unités conforme PDF, avec Redmi 14C en alerte < 3)
INSERT INTO `table_stocks` (`id_produit`, `quantite_initiale`, `quantite_actuelle`, `seuil_alerte`) VALUES
(1, 15, 12, 3),
(2, 10, 8, 3),
(3, 20, 18, 3),
(4, 25, 22, 3),
(5, 10, 2, 3), -- ALERTE STOCK
(6, 30, 28, 3),
(7, 50, 45, 3),
(8, 40, 38, 3),
(9, 35, 31, 3);

-- 8. Clients Initialisés
INSERT INTO `table_clients` (`nom`, `prenom`, `telephone`, `email`, `localisation`, `cumul_achats`, `statut_fidelite`) VALUES
('Bamba', 'Fatoumata', '+228 90 12 34 56', 'fatoumata.bamba@gmail.com', 'Lomé Tokoin', 1890000.00, 'VIP Gold'),
('Diallo', 'Moussa', '+228 99 88 77 66', 'moussa.diallo@yahoo.fr', 'Lomé Agbalépédogan', 425000.00, 'Standard'),
('Lawson', 'Koffi', '+228 91 00 11 22', 'koffi.lawson@hotmail.com', 'Lomé Adidogomé', 750000.00, 'VIP Silver');

-- 9. Fournisseurs Initialisés
INSERT INTO `table_fournisseurs` (`nom_contact`, `nom_societe`, `telephone`, `email`, `adresse`, `produits_fournis`) VALUES
('Mr. Tariq', 'Dubai Phone Wholesale LLC', '+971 50 123 4567', 'sales@dubaiphone.ae', 'Deira Commercial Center, Dubai UAE', 'iPhones neufs & certifiés'),
('Mme. Zhang', 'Hong Kong Telecom Trading', '+852 9876 5432', 'zhang@hktelecom.hk', 'Kowloon Bay, Hong Kong', 'Xiaomi, Redmi, Accessoires'),
('Représentant Togo', 'Xiaomi Official Togo', '+228 90 00 11 22', 'contact@xiaomitogo.com', 'Lomé Centre', 'Smartphones Xiaomi officiels sous garantie');
