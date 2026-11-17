<?php
/**
 * Contrôleur Trocs & Reprises d'Appareils
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class TrocController {

    public static function createTroc($idClient, $idUser, $appareilRepris, $valeurReprise, $idProduitPris, $prixVentePris) {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            $codeTroc = 'TRC-2026-' . rand(100, 999);
            $soulteFinanciere = max(0, $prixVentePris - $valeurReprise);

            $stmtProduit = $db->prepare("SELECT nom_produit, modele_exact FROM table_produits WHERE id_produit = :id");
            $stmtProduit->execute([':id' => $idProduitPris]);
            $produit = $stmtProduit->fetch();
            $nomProduit = $produit['nom_produit'] ?? $produit['modele_exact'] ?? 'Produit supprimé';

            $stmt = $db->prepare("
                INSERT INTO table_trocs (code_troc, id_client, id_user, appareil_repris, valeur_reprise, id_produit_pris, prix_vente_pris, soulte_financiere, produit_pris_nom)
                VALUES (:code_troc, :id_client, :id_user, :appareil_repris, :valeur_reprise, :id_produit_pris, :prix_vente_pris, :soulte_financiere, :produit_pris_nom)
            ");
            $stmt->execute([
                ':code_troc' => $codeTroc,
                ':id_client' => ($idClient > 0 ? $idClient : 1),
                ':id_user' => ($idUser > 0 ? $idUser : 1),
                ':appareil_repris' => $appareilRepris,
                ':valeur_reprise' => $valeurReprise,
                ':id_produit_pris' => ($idProduitPris > 0 ? $idProduitPris : 1),
                ':prix_vente_pris' => $prixVentePris,
                ':soulte_financiere' => $soulteFinanciere,
                ':produit_pris_nom' => $nomProduit
            ]);

            // Insérer l'appareil d'occasion en stock IMEI avec statut "Reçu Troc"
            $imeiTmp = '351029' . rand(100000000, 999999999);
            $stmtIMEI = $db->prepare("
                INSERT INTO table_imei (id_produit, imei1, etat_batterie_pct, statut)
                VALUES (1, :imei, 88, 'Reçu Troc')
            ");
            $stmtIMEI->execute([':imei' => $imeiTmp]);

            $db->commit();
            return [
                'success' => true,
                'message' => 'Troc enregistré avec succès !',
                'code_troc' => $codeTroc,
                'soulte_financiere' => $soulteFinanciere
            ];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Erreur troc : ' . $e->getMessage()];
        }
    }

    public static function getTrocs() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT t.id_troc, t.code_troc, t.date_troc, CONCAT(c.nom, ' ', c.prenom) as client_nom,
                   t.appareil_repris, t.valeur_reprise, COALESCE(t.produit_pris_nom, p.modele_exact, 'Produit supprimé') as produit_pris, t.prix_vente_pris, t.soulte_financiere
            FROM table_trocs t
            LEFT JOIN table_clients c ON t.id_client = c.id_client
            LEFT JOIN table_produits p ON t.id_produit_pris = p.id_produit
            ORDER BY t.date_troc DESC
        ");
        return ['success' => true, 'trocs' => $stmt->fetchAll()];
    }
}
