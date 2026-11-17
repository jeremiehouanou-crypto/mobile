<?php
/**
 * Configuration & Connexion Base de Données MySQL (PDO)
 * Projet : SGC-SMARTPHONE - MOBILE HOME TOGO
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'mobilehome_sgc_corrige');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            require_once __DIR__ . '/connexion.php';
            $this->pdo = $pdo;
        } catch (PDOException $e) {
            // Mode dégradé ou affichage d'erreur JSON en API
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
                exit;
            }
            throw new Exception("Erreur de connexion BDD: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
