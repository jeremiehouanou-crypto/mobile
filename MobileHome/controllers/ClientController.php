<?php
/**
 * Contrôleur Répertoire Clients & Fidélité
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class ClientController {

    public static function getClients() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM table_clients ORDER BY cumul_achats DESC");
        return ['success' => true, 'clients' => $stmt->fetchAll()];
    }

    public static function addClient($nom, $prenom, $telephone, $email = null, $localisation = 'Lomé') {
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO table_clients (nom, prenom, telephone, email, localisation) VALUES (:nom, :prenom, :telephone, :email, :localisation)");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':telephone' => $telephone,
                ':email' => $email,
                ':localisation' => $localisation
            ]);
            return ['success' => true, 'message' => 'Client enregistré dans le répertoire !', 'id_client' => $db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur ajout client: ' . $e->getMessage()];
        }
    }
}
