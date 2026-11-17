<?php
/**
 * Contrôleur SAV & Réparations (Tickets d'Atelier)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class SAVController {

    public static function createTicket($idClient, $idUser, $appareil, $descriptionPanne, $acompte, $devisEst) {
        $db = Database::getInstance();
        try {
            $codeTicket = 'SAV-2026-' . rand(100, 999);
            $stmt = $db->prepare("
                INSERT INTO table_reparations (code_ticket, id_client, id_user, appareil, description_panne, acompte, devis_est, statut)
                VALUES (:code_ticket, :id_client, :id_user, :appareil, :description_panne, :acompte, :devis_est, 'En Atelier')
            ");
            $stmt->execute([
                ':code_ticket' => $codeTicket,
                ':id_client' => ($idClient > 0 ? $idClient : 1),
                ':id_user' => ($idUser > 0 ? $idUser : 1),
                ':appareil' => $appareil,
                ':description_panne' => $descriptionPanne,
                ':acompte' => $acompte,
                ':devis_est' => $devisEst
            ]);

            return ['success' => true, 'message' => 'Ticket de réparation ouvert avec succès !', 'code_ticket' => $codeTicket];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur SAV: ' . $e->getMessage()];
        }
    }

    public static function getReparations() {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT r.id_reparation, r.code_ticket, r.date_reparation, CONCAT(c.nom, ' ', c.prenom) as client_nom,
                   r.appareil, r.description_panne, r.acompte, r.devis_est, r.statut
            FROM table_reparations r
            LEFT JOIN table_clients c ON r.id_client = c.id_client
            ORDER BY r.date_reparation DESC
        ");
        return ['success' => true, 'reparations' => $stmt->fetchAll()];
    }
}
