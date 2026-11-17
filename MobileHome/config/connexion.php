<?php
/**
 * Fichier de Connexion à la Base de Données MySQL (PDO)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 * 
 * Ce fichier établit la connexion entre nos scripts PHP et la base de données MySQL.
 * Il est inclus au début de chaque page nécessitant des accès aux données.
 */

// Paramètres de connexion BDD
$hote     = 'localhost';
$port     = '3306';
$nomBdd   = 'mobilehome_sgc_corrige';
$utilisateur = 'root';
$motDePasse  = '';

try {
    // Création de l'objet PDO (PHP Data Objects) avec gestion de l'encodage UTF-8
    $pdo = new PDO(
        "mysql:host=$hote;port=$port;dbname=$nomBdd;charset=utf8mb4",
        $utilisateur,
        $motDePasse,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Active les exceptions en cas d'erreur SQL
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retourne les résultats sous forme de tableau associatif
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise de vraies requêtes préparées pour la sécurité
        ]
    );

    // Auto-migration & compatibilité de schéma BDD
    try {
        // 1. Compatibilité table_produits
        $colsProd = $pdo->query("SHOW COLUMNS FROM table_produits")->fetchAll(PDO::FETCH_COLUMN);
        if ($colsProd) {
            if (!in_array('nom_produit', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN nom_produit VARCHAR(150) DEFAULT NULL");
                if (in_array('modele_exact', $colsProd)) {
                    $pdo->exec("UPDATE table_produits SET nom_produit = modele_exact WHERE nom_produit IS NULL OR nom_produit = ''");
                }
            }
            if (!in_array('stockage_ram', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN stockage_ram VARCHAR(50) DEFAULT '256Go / 8Go'");
            }
            if (!in_array('prix_vente', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN prix_vente DECIMAL(12,2) DEFAULT 0.00");
            }
            if (!in_array('prix_achat', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN prix_achat DECIMAL(12,2) DEFAULT 0.00");
            }
            if (!in_array('quantite_stock', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN quantite_stock INT DEFAULT 10");
            }
            if (!in_array('seuil_alerte', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN seuil_alerte INT DEFAULT 3");
            }
            if (!in_array('image_url', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN image_url VARCHAR(255) DEFAULT NULL");
            }
            if (!in_array('garantie', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN garantie VARCHAR(50) DEFAULT '12 Mois'");
            }
            if (!in_array('couleurs', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN couleurs VARCHAR(255) DEFAULT 'Titane, Noir, Argent'");
            }
            if (!in_array('description', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN description TEXT DEFAULT NULL");
            }
            if (!in_array('generation', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN generation TINYINT UNSIGNED DEFAULT NULL");
            }
            if (!in_array('processeur', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN processeur VARCHAR(120) DEFAULT NULL");
            }
            if (!in_array('carte_graphique', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN carte_graphique VARCHAR(120) DEFAULT NULL");
            }
            if (!in_array('type_pc', $colsProd)) {
                $pdo->exec("ALTER TABLE table_produits ADD COLUMN type_pc VARCHAR(40) NOT NULL DEFAULT 'Bureautique'");
            }
        }

        $pdo->exec("INSERT IGNORE INTO table_marques (nom_marque) VALUES ('HP')");
        $pdo->exec("UPDATE table_entreprise SET nom = 'JY SHOP STORE', slogan = 'PC HP - 8e a 13e generation' WHERE nom = 'MOBILE HOME TOGO'");
        $pdo->exec("UPDATE table_entreprise
            SET tel1 = IF(tel1 IS NULL OR tel1 = '', '+229 90 38 90 62', tel1),
                whatsapp = IF(whatsapp IS NULL OR whatsapp = '', '+229 90 38 90 62', whatsapp)
            WHERE nom = 'JY SHOP STORE'");
        $pdo->exec("UPDATE table_produits SET type_pc = 'À reclasser'
            WHERE generation IS NULL AND (reseau IS NULL OR reseau <> 'PC') AND type_pc = 'Bureautique'");

        // Préserver les libellés historiques tout en autorisant la suppression d'un produit.
        $colsLignes = $pdo->query("SHOW COLUMNS FROM table_lignes_vente")->fetchAll(PDO::FETCH_COLUMN);
        if ($colsLignes) {
            if (!in_array('produit_nom', $colsLignes)) {
                $pdo->exec("ALTER TABLE table_lignes_vente ADD COLUMN produit_nom VARCHAR(150) DEFAULT NULL");
            }
            if (!in_array('produit_stockage_ram', $colsLignes)) {
                $pdo->exec("ALTER TABLE table_lignes_vente ADD COLUMN produit_stockage_ram VARCHAR(50) DEFAULT NULL");
            }
            $pdo->exec("UPDATE table_lignes_vente l
                LEFT JOIN table_produits p ON p.id_produit = l.id_produit
                SET l.produit_nom = COALESCE(l.produit_nom, p.nom_produit, p.modele_exact, 'Produit supprimé'),
                    l.produit_stockage_ram = COALESCE(l.produit_stockage_ram, p.stockage_ram, '')");
        }

        $colsTrocs = $pdo->query("SHOW COLUMNS FROM table_trocs")->fetchAll(PDO::FETCH_COLUMN);
        if ($colsTrocs) {
            if (!in_array('produit_pris_nom', $colsTrocs)) {
                $pdo->exec("ALTER TABLE table_trocs ADD COLUMN produit_pris_nom VARCHAR(150) DEFAULT NULL");
            }
            $pdo->exec("UPDATE table_trocs t
                LEFT JOIN table_produits p ON p.id_produit = t.id_produit_pris
                SET t.produit_pris_nom = COALESCE(t.produit_pris_nom, p.nom_produit, p.modele_exact, 'Produit supprimé')");
        }

        $ensureNullableProductReference = static function ($table, $column) use ($pdo) {
            $stmt = $pdo->prepare("SELECT k.CONSTRAINT_NAME, r.DELETE_RULE
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
                JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS r
                  ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA
                 AND r.TABLE_NAME = k.TABLE_NAME
                 AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                WHERE k.TABLE_SCHEMA = DATABASE()
                  AND k.TABLE_NAME = :table
                  AND k.COLUMN_NAME = :column
                  AND k.REFERENCED_TABLE_NAME = 'table_produits'");
            $stmt->execute([':table' => $table, ':column' => $column]);
            $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $hasSetNull = false;
            foreach ($foreignKeys as $foreignKey) {
                if (strtoupper($foreignKey['DELETE_RULE']) === 'SET NULL') {
                    $hasSetNull = true;
                    continue;
                }
                $constraint = str_replace('`', '``', $foreignKey['CONSTRAINT_NAME']);
                $pdo->exec("ALTER TABLE `$table` DROP FOREIGN KEY `$constraint`");
            }

            $columns = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $columnInfo) {
                if ($columnInfo['Field'] === $column && $columnInfo['Null'] !== 'YES') {
                    $pdo->exec("ALTER TABLE `$table` MODIFY `$column` INT NULL");
                    break;
                }
            }

            if (!$hasSetNull) {
                $constraint = "fk_{$table}_{$column}_delete_set_null";
                $pdo->exec("ALTER TABLE `$table`
                    ADD CONSTRAINT `$constraint`
                    FOREIGN KEY (`$column`) REFERENCES table_produits (id_produit)
                    ON DELETE SET NULL");
            }
        };

        $ensureNullableProductReference('table_lignes_vente', 'id_produit');
        $ensureNullableProductReference('table_trocs', 'id_produit_pris');
        $ensureNullableProductReference('table_imei', 'id_produit');

        // 2. Compatibilité table_utilisateurs
        $colsUser = $pdo->query("SHOW COLUMNS FROM table_utilisateurs")->fetchAll(PDO::FETCH_COLUMN);
        if ($colsUser) {
            if (!in_array('mot_de_passe', $colsUser)) {
                $pdo->exec("ALTER TABLE table_utilisateurs ADD COLUMN mot_de_passe VARCHAR(255) DEFAULT NULL");
                if (in_array('mot_de_passe_hash', $colsUser)) {
                    $pdo->exec("UPDATE table_utilisateurs SET mot_de_passe = mot_de_passe_hash WHERE mot_de_passe IS NULL");
                }
            }
        }
    } catch (Exception $schemaEx) {
        // En cas d'absence des tables avant initialisation, ignorer silencieusement
    }

} catch (PDOException $e) {
    // Message clair en cas d'erreur de connexion
    die("<div style='font-family:sans-serif; padding:20px; color:red;'>
            <h2>Erreur de Connexion à la Base de Données MySQL</h2>
            <p>" . $e->getMessage() . "</p>
            <p>Veuillez vérifier que MySQL est démarré et que la base <strong>$nomBdd</strong> existe.</p>
         </div>");
}

function obtenirIdCapacitePc(PDO $pdo, $specifications) {
    $specifications = trim((string)$specifications);

    if (!preg_match('/(\d+)\s*(?:Go|GB)?\s*RAM\b/i', $specifications, $ramMatch)
        || !preg_match('/(\d+)\s*(?:Go|GB)?\s*(?:SSD|HDD|NVMe|eMMC)\b/i', $specifications, $stockageMatch)) {
        throw new InvalidArgumentException('Indiquez les caractéristiques au format « 16 Go RAM / 512 Go SSD ».');
    }

    $ramGo = (int)$ramMatch[1];
    $stockageGo = (int)$stockageMatch[1];
    if ($ramGo < 1 || $stockageGo < 1) {
        throw new InvalidArgumentException('La RAM et le stockage doivent être supérieurs à zéro.');
    }

    $stmt = $pdo->prepare('SELECT id_capacite FROM table_capacites WHERE ram_go = :ram AND stockage_go = :stockage LIMIT 1');
    $stmt->execute([':ram' => $ramGo, ':stockage' => $stockageGo]);
    $idCapacite = $stmt->fetchColumn();

    if ($idCapacite !== false) {
        return (int)$idCapacite;
    }

    $stmt = $pdo->prepare('INSERT INTO table_capacites (stockage_go, ram_go) VALUES (:stockage, :ram)');
    $stmt->execute([':stockage' => $stockageGo, ':ram' => $ramGo]);

    return (int)$pdo->lastInsertId();
}

/**
 * Fonction de sécurité pour vérifier si un utilisateur est bien connecté.
 * Redirige automatiquement vers login.php si aucune session n'est active.
 */
function verifierConnexion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['utilisateur'])) {
        header('Location: login.php');
        exit;
    }
}
