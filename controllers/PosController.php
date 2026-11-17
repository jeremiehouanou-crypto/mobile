<?php
/**
 * Contrôleur Caisse POS (Encaissement, Ventes & Invoices)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class PosController {

    public static function createSale($idClient, $idUser, $cartItems, $modePaiement, $remise = 0) {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();

            $totalHT = 0;
            $totalBenefice = 0;

            foreach ($cartItems as $item) {
                $totalHT += $item['price'] * $item['qty'];
            }

            $totalTTC = max(0, $totalHT - $remise);
            $codeFacture = 'FAC-2026-' . rand(1000, 9999);

            // 1. Insertion Vente
            $stmtVente = $db->prepare("
                INSERT INTO table_ventes (code_facture, id_client, id_user, total_ht, remise, total_ttc, mode_paiement)
                VALUES (:code_facture, :id_client, :id_user, :total_ht, :remise, :total_ttc, :mode_paiement)
            ");
            $stmtVente->execute([
                ':code_facture' => $codeFacture,
                ':id_client' => ($idClient > 0 ? $idClient : null),
                ':id_user' => ($idUser > 0 ? $idUser : 1),
                ':total_ht' => $totalHT,
                ':remise' => $remise,
                ':total_ttc' => $totalTTC,
                ':mode_paiement' => $modePaiement
            ]);

            $idVente = $db->lastInsertId();

            // 2. Insertion Lignes de Vente & Mouvements de Stock
            foreach ($cartItems as $item) {
                $idProduit = isset($item['id_produit']) ? $item['id_produit'] : 1;
                $prixUnitaire = $item['price'];
                $qty = $item['qty'];
                $benefice = ($prixUnitaire * 0.22) * $qty; // Est. Marge moyenne 22%

                $stmtProduit = $db->prepare("SELECT nom_produit, modele_exact, stockage_ram FROM table_produits WHERE id_produit = :id");
                $stmtProduit->execute([':id' => $idProduit]);
                $produit = $stmtProduit->fetch();
                $nomProduit = $produit['nom_produit'] ?? $produit['modele_exact'] ?? 'Produit supprimé';
                $stockageProduit = $produit['stockage_ram'] ?? '';

                $stmtLigne = $db->prepare("
                    INSERT INTO table_lignes_vente (id_vente, id_produit, quantite, prix_unitaire, benefice_ligne, produit_nom, produit_stockage_ram)
                    VALUES (:id_vente, :id_produit, :quantite, :prix_unitaire, :benefice_ligne, :produit_nom, :produit_stockage_ram)
                ");
                $stmtLigne->execute([
                    ':id_vente' => $idVente,
                    ':id_produit' => $idProduit,
                    ':quantite' => $qty,
                    ':prix_unitaire' => $prixUnitaire,
                    ':benefice_ligne' => $benefice,
                    ':produit_nom' => $nomProduit,
                    ':produit_stockage_ram' => $stockageProduit
                ]);

                // Décrémenter Stock
                $stmtDecStock = $db->prepare("UPDATE table_stocks SET quantite_actuelle = GREATEST(0, quantite_actuelle - :qty) WHERE id_produit = :id_produit");
                $stmtDecStock->execute([':qty' => $qty, ':id_produit' => $idProduit]);

                // Si IMEI fourni, marquer comme vendu
                if (!empty($item['imei'])) {
                    $stmtIMEI = $db->prepare("UPDATE table_imei SET statut = 'Vendu' WHERE imei1 = :imei");
                    $stmtIMEI->execute([':imei' => $item['imei']]);
                }
            }

            // 3. Insertion Mode de Paiement
            $stmtPaiement = $db->prepare("
                INSERT INTO table_paiements (id_vente, moyen_paiement, montant)
                VALUES (:id_vente, :moyen_paiement, :montant)
            ");
            $stmtPaiement->execute([
                ':id_vente' => $idVente,
                ':moyen_paiement' => $modePaiement,
                ':montant' => $totalTTC
            ]);

            // 4. Cumul Achats Client
            if ($idClient > 0) {
                $stmtClient = $db->prepare("UPDATE table_clients SET cumul_achats = cumul_achats + :montant WHERE id_client = :id_client");
                $stmtClient->execute([':montant' => $totalTTC, ':id_client' => $idClient]);
            }

            $db->commit();

            return [
                'success' => true,
                'message' => 'Vente enregistrée avec succès !',
                'code_facture' => $codeFacture,
                'id_vente' => $idVente,
                'total_ttc' => $totalTTC
            ];

        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de la vente : ' . $e->getMessage()];
        }
    }

    public static function getSalesHistory() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT v.id_vente, v.code_facture, v.date_vente, COALESCE(CONCAT(c.nom, ' ', c.prenom), 'Client de Passage') as client_nom,
                   v.mode_paiement, v.total_ttc
            FROM table_ventes v
            LEFT JOIN table_clients c ON v.id_client = c.id_client
            ORDER BY v.date_vente DESC
        ");
        return ['success' => true, 'sales' => $stmt->fetchAll()];
    }
}
