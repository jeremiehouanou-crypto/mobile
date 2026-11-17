<?php
/**
 * Contrôleur Répertoire Fournisseurs & Grossistes
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class FournisseurController {

    public static function getFournisseurs() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM table_fournisseurs ORDER BY id_fournisseur ASC");
        return ['success' => true, 'fournisseurs' => $stmt->fetchAll()];
    }

    public static function addFournisseur($nomContact, $nomSociete, $telephone, $email, $produitsFournis) {
        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("INSERT INTO table_fournisseurs (nom_contact, nom_societe, telephone, email, produits_fournis) VALUES (:nom_contact, :nom_societe, :telephone, :email, :produits_fournis)");
            $stmt->execute([
                ':nom_contact' => $nomContact,
                ':nom_societe' => $nomSociete,
                ':telephone' => $telephone,
                ':email' => $email,
                ':produits_fournis' => $produitsFournis
            ]);
            return ['success' => true, 'message' => 'Fournisseur enregistré !', 'id_fournisseur' => $db->lastInsertId()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur fournisseur: ' . $e->getMessage()];
        }
    }
}
