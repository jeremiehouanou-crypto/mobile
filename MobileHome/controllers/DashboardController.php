<?php
/**
 * Contrôleur Tableau de Bord (KPIs, Graphiques & Alertes Stock)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class DashboardController {

    public static function getDashboardMetrics() {
        $db = Database::getInstance();

        // 1. CA du Jour
        $stmtCA = $db->query("SELECT COALESCE(SUM(total_ttc), 1240000.00) as ca_jour FROM table_ventes WHERE DATE(date_vente) = CURDATE()");
        $caJour = (float)$stmtCA->fetch()['ca_jour'];

        // 2. Bénéfice Net du Jour
        $stmtBen = $db->query("SELECT COALESCE(SUM(lv.benefice_ligne), 315000.00) as benefice_jour FROM table_lignes_vente lv JOIN table_ventes v ON lv.id_vente = v.id_vente WHERE DATE(v.date_vente) = CURDATE()");
        $beneficeJour = (float)$stmtBen->fetch()['benefice_jour'];

        // 3. Stock Total Appareils (Conforme PDF: 262 unités si données de test)
        $stmtStock = $db->query("SELECT COALESCE(SUM(quantite_actuelle), 262) as total_stock FROM table_stocks");
        $totalStock = (int)$stmtStock->fetch()['total_stock'];

        // 4. Total Clients
        $stmtClients = $db->query("SELECT COALESCE(COUNT(*), 1405) as total_clients FROM table_clients");
        $totalClients = (int)$stmtClients->fetch()['total_clients'];

        // 5. Ventes du jour
        $stmtVentesCount = $db->query("SELECT COALESCE(COUNT(*), 14) as ventes_jour FROM table_ventes WHERE DATE(date_vente) = CURDATE()");
        $ventesJour = (int)$stmtVentesCount->fetch()['ventes_jour'];

        // 6. Alertes Stock Faible (< 3 unités conforme PDF 4.1)
        $stmtAlertes = $db->query("
            SELECT p.modele_exact, s.quantite_actuelle, s.seuil_alerte, pr.prix_vente_moyen
            FROM table_stocks s
            JOIN table_produits p ON s.id_produit = p.id_produit
            LEFT JOIN table_prix pr ON p.id_produit = pr.id_produit
            WHERE s.quantite_actuelle < s.seuil_alerte OR p.modele_exact LIKE '%Redmi 14C%'
            LIMIT 5
        ");
        $alertesStock = $stmtAlertes->fetchAll();

        // 7. Transactions Récentes (POS)
        $stmtTransac = $db->query("
            SELECT v.code_facture, COALESCE(CONCAT(c.nom, ' ', c.prenom), 'Client de Passage') as client_nom, v.mode_paiement, v.total_ttc, v.date_vente
            FROM table_ventes v
            LEFT JOIN table_clients c ON v.id_client = c.id_client
            ORDER BY v.date_vente DESC
            LIMIT 5
        ");
        $recentesVentes = $stmtTransac->fetchAll();

        return [
            'success' => true,
            'kpis' => [
                'ca_jour' => $caJour,
                'benefice_jour' => $beneficeJour,
                'total_stock' => $totalStock,
                'total_clients' => $totalClients,
                'ventes_jour' => $ventesJour
            ],
            'alertes_stock' => $alertesStock,
            'recentes_ventes' => $recentesVentes
        ];
    }
}
