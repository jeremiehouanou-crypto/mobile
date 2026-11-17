<?php
/**
 * Contrôleur Utilisateurs & Droits d'Accès (RBAC)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class UserController {

    public static function getUsers() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT id_user, nom, identifiant, role, statut, date_creation FROM table_utilisateurs ORDER BY id_user ASC");
        return ['success' => true, 'users' => $stmt->fetchAll()];
    }

    public static function addUser($nom, $identifiant, $password, $role = 'Vendeur') {
        $db = Database::getInstance();
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO table_utilisateurs (nom, identifiant, mot_de_passe_hash, role) VALUES (:nom, :identifiant, :hash, :role)");
            $stmt->execute([
                ':nom' => $nom,
                ':identifiant' => $identifiant,
                ':hash' => $hash,
                ':role' => $role
            ]);
            return ['success' => true, 'message' => 'Utilisateur créé avec succès !', 'id_user' => $db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur création utilisateur: ' . $e->getMessage()];
        }
    }
}
