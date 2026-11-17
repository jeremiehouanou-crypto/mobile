<?php
/**
 * Contrôleur de gestion du catalogue des produits
 * Projet : MobileHome Togo - Système de Gestion Commerciale (SGC)
 */
require_once __DIR__ . '/../config/database.php';

class ProductController {

    public static function getAllProducts() {
        $db = Database::getInstance();
        $stmt = $db->query("
                     SELECT p.id_produit, p.nom_produit, m.nom_marque, s.nom_serie, p.modele_exact,
                         p.stockage_ram, p.generation, p.processeur, p.carte_graphique, p.type_pc,
                         p.image_url, p.description, p.garantie, p.prix_vente, p.quantite_stock,
                         pr.prix_achat, pr.prix_vente_moyen, st.quantite_actuelle
            FROM table_produits p
            JOIN table_marques m ON p.id_marque = m.id_marque
            LEFT JOIN table_series s ON p.id_serie = s.id_serie
            LEFT JOIN table_prix pr ON p.id_produit = pr.id_produit
            LEFT JOIN table_stocks st ON p.id_produit = st.id_produit
            WHERE p.type_pc <> 'À reclasser'
            ORDER BY p.id_produit DESC
        ");
        return ['success' => true, 'products' => $stmt->fetchAll()];
    }

    public static function addProduct($idMarque, $modeleExact, $stockageRam, $prixVente, $generation = null, $processeur = '', $carteGraphique = '', $typePc = 'Bureautique', $garantie = '3 mois') {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $idCapacite = obtenirIdCapacitePc($db, $stockageRam);

            $stmt = $db->prepare("INSERT INTO table_produits (id_marque, id_capacite, nom_produit, modele_exact, stockage_ram, reseau, generation, processeur, carte_graphique, type_pc, prix_vente, garantie) VALUES (:id_marque, :id_capacite, :nom_produit, :modele_exact, :stockage_ram, 'PC', :generation, :processeur, :carte_graphique, :type_pc, :prix_vente, :garantie)");
            $stmt->execute([
                ':id_marque' => $idMarque,
                ':id_capacite' => $idCapacite,
                ':nom_produit' => $modeleExact,
                ':modele_exact' => $modeleExact,
                ':stockage_ram' => $stockageRam,
                ':generation' => ($generation >= 8 && $generation <= 13 ? $generation : null),
                ':processeur' => $processeur,
                ':carte_graphique' => $carteGraphique,
                ':type_pc' => $typePc,
                ':prix_vente' => $prixVente,
                ':garantie' => $garantie
            ]);
            $idProduit = $db->lastInsertId();

            $prixAchat = $prixVente * 0.8;
            $stmtPrix = $db->prepare("INSERT INTO table_prix (id_produit, prix_achat, prix_vente_moyen) VALUES (:id_produit, :prix_achat, :prix_vente)");
            $stmtPrix->execute([':id_produit' => $idProduit, ':prix_achat' => $prixAchat, ':prix_vente' => $prixVente]);

            $stmtStock = $db->prepare("INSERT INTO table_stocks (id_produit, quantite_initiale, quantite_actuelle, seuil_alerte) VALUES (:id_produit, 5, 5, 3)");
            $stmtStock->execute([':id_produit' => $idProduit]);

            $db->commit();
            return ['success' => true, 'message' => 'Produit ajouté au catalogue avec succès !', 'id_produit' => $idProduit];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Erreur ajout produit: ' . $e->getMessage()];
        }
    }
}
