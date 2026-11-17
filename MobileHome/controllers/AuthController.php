<?php
/**
 * Contrôleur Authentification & Session (RBAC)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE)
 */

require_once __DIR__ . '/../config/database.php';

class AuthController {
    
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($identifiant, $password) {
        self::initSession();
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM table_utilisateurs WHERE identifiant = :identifiant AND statut = 'Actif'");
        $stmt->execute([':identifiant' => $identifiant]);
        $user = $stmt->fetch();

        $passInDb = isset($user['mot_de_passe']) ? $user['mot_de_passe'] : (isset($user['mot_de_passe_hash']) ? $user['mot_de_passe_hash'] : '');

        // En environnement de dev / démo si le mot de passe correspond à la chaîne brute 'admin2026', 'vendeur2026', 'admin', 'vendeur' ou hash
        if ($user && (
            $password === 'admin2026' || 
            $password === 'vendeur2026' || 
            $password === 'admin' || 
            $password === 'vendeur' || 
            $password === $passInDb || 
            password_verify($password, $passInDb)
        )) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['identifiant'] = $user['identifiant'];

            return [
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user['id_user'],
                    'nom' => $user['nom'],
                    'role' => $user['role'],
                    'identifiant' => $user['identifiant']
                ]
            ];
        }

        return ['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'];
    }

    public static function logout() {
        self::initSession();
        session_destroy();
        return ['success' => true, 'message' => 'Déconnexion réussie'];
    }

    public static function getCurrentUser() {
        self::initSession();
        if (isset($_SESSION['user_id'])) {
            return [
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'nom' => $_SESSION['user_nom'],
                    'role' => $_SESSION['user_role'],
                    'identifiant' => $_SESSION['identifiant']
                ]
            ];
        }
        // Par défaut en démo si pas de session : Admin connecté
        return [
            'logged_in' => true,
            'user' => [
                'id' => 1,
                'nom' => 'Direction Générale',
                'role' => 'Admin',
                'identifiant' => 'admin'
            ]
        ];
    }
}
