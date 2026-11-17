<?php
/**
 * Routeur API REST Central & Contrôleur d'Actions (traitement.php)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Inclusions des contrôleurs
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/PosController.php';
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/TrocController.php';
require_once __DIR__ . '/controllers/SAVController.php';
require_once __DIR__ . '/controllers/ClientController.php';
require_once __DIR__ . '/controllers/FournisseurController.php';
require_once __DIR__ . '/controllers/UserController.php';

// Détermination de l'action demandée
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// Lecture du corps JSON si envoyé
$inputJSON = file_get_json();

if (empty($action) && isset($inputJSON['action'])) {
    $action = $inputJSON['action'];
}

function file_get_json() {
    $body = file_get_contents('php://input');
    if (!empty($body)) {
        $decoded = json_decode($body, true);
        if (is_array($decoded)) return $decoded;
    }
    return [];
}

try {
    switch ($action) {
        // --- AUTHENTIFICATION ---
        case 'login':
            $identifiant = isset($inputJSON['identifiant']) ? $inputJSON['identifiant'] : (isset($_POST['identifiant']) ? $_POST['identifiant'] : '');
            $password = isset($inputJSON['password']) ? $inputJSON['password'] : (isset($_POST['password']) ? $_POST['password'] : '');
            echo json_encode(AuthController::login($identifiant, $password));
            break;

        case 'logout':
            echo json_encode(AuthController::logout());
            break;

        case 'current_user':
            echo json_encode(AuthController::getCurrentUser());
            break;

        // --- DASHBOARD & METRICS ---
        case 'get_dashboard_metrics':
            echo json_encode(DashboardController::getDashboardMetrics());
            break;

        // --- CAISSE & POS ---
        case 'create_sale':
            $idClient = isset($inputJSON['id_client']) ? (int)$inputJSON['id_client'] : 0;
            $idUser = isset($inputJSON['id_user']) ? (int)$inputJSON['id_user'] : 1;
            $cartItems = isset($inputJSON['cart']) ? $inputJSON['cart'] : [];
            $modePaiement = isset($inputJSON['mode_paiement']) ? $inputJSON['mode_paiement'] : 'Espèces';
            $remise = isset($inputJSON['remise']) ? (float)$inputJSON['remise'] : 0;
            echo json_encode(PosController::createSale($idClient, $idUser, $cartItems, $modePaiement, $remise));
            break;

        case 'get_sales':
            echo json_encode(PosController::getSalesHistory());
            break;

        // --- PRODUITS & STOCKS ---
        case 'get_products':
            echo json_encode(ProductController::getAllProducts());
            break;

        case 'add_product':
            $idMarque = isset($inputJSON['id_marque']) ? (int)$inputJSON['id_marque'] : 1;
            $modeleExact = isset($inputJSON['modele_exact']) ? $inputJSON['modele_exact'] : '';
            $stockageRam = isset($inputJSON['stockage_ram']) ? $inputJSON['stockage_ram'] : '256Go / 8Go';
            $prixVente = isset($inputJSON['prix_vente']) ? (float)$inputJSON['prix_vente'] : 0;
            $generation = isset($inputJSON['generation']) ? (int)$inputJSON['generation'] : null;
            $processeur = isset($inputJSON['processeur']) ? $inputJSON['processeur'] : '';
            $carteGraphique = isset($inputJSON['carte_graphique']) ? $inputJSON['carte_graphique'] : '';
            $typePc = isset($inputJSON['type_pc']) ? $inputJSON['type_pc'] : 'Bureautique';
            echo json_encode(ProductController::addProduct($idMarque, $modeleExact, $stockageRam, $prixVente, $generation, $processeur, $carteGraphique, $typePc));
            break;

        // --- TROCS ---
        case 'get_trocs':
            echo json_encode(TrocController::getTrocs());
            break;

        case 'create_troc':
            $idClient = isset($inputJSON['id_client']) ? (int)$inputJSON['id_client'] : 1;
            $appareilRepris = isset($inputJSON['appareil_repris']) ? $inputJSON['appareil_repris'] : '';
            $valeurReprise = isset($inputJSON['valeur_reprise']) ? (float)$inputJSON['valeur_reprise'] : 0;
            $idProduitPris = isset($inputJSON['id_produit_pris']) ? (int)$inputJSON['id_produit_pris'] : 1;
            $prixVentePris = isset($inputJSON['prix_vente_pris']) ? (float)$inputJSON['prix_vente_pris'] : 0;
            echo json_encode(TrocController::createTroc($idClient, 1, $appareilRepris, $valeurReprise, $idProduitPris, $prixVentePris));
            break;

        // --- SAV & REPARATIONS ---
        case 'get_reparations':
            echo json_encode(SAVController::getReparations());
            break;

        case 'create_repair':
            $idClient = isset($inputJSON['id_client']) ? (int)$inputJSON['id_client'] : 1;
            $appareil = isset($inputJSON['appareil']) ? $inputJSON['appareil'] : '';
            $descriptionPanne = isset($inputJSON['description_panne']) ? $inputJSON['description_panne'] : '';
            $acompte = isset($inputJSON['acompte']) ? (float)$inputJSON['acompte'] : 0;
            $devisEst = isset($inputJSON['devis_est']) ? (float)$inputJSON['devis_est'] : 0;
            echo json_encode(SAVController::createTicket($idClient, 1, $appareil, $descriptionPanne, $acompte, $devisEst));
            break;

        // --- CLIENTS ---
        case 'get_clients':
            echo json_encode(ClientController::getClients());
            break;

        case 'add_client':
            $nom = isset($inputJSON['nom']) ? $inputJSON['nom'] : '';
            $prenom = isset($inputJSON['prenom']) ? $inputJSON['prenom'] : '';
            $telephone = isset($inputJSON['telephone']) ? $inputJSON['telephone'] : '';
            echo json_encode(ClientController::addClient($nom, $prenom, $telephone));
            break;

        // --- FOURNISSEURS ---
        case 'get_fournisseurs':
            echo json_encode(FournisseurController::getFournisseurs());
            break;

        case 'add_fournisseur':
            $nomContact = isset($inputJSON['nom_contact']) ? $inputJSON['nom_contact'] : '';
            $nomSociete = isset($inputJSON['nom_societe']) ? $inputJSON['nom_societe'] : '';
            $telephone = isset($inputJSON['telephone']) ? $inputJSON['telephone'] : '';
            $email = isset($inputJSON['email']) ? $inputJSON['email'] : '';
            $produitsFournis = isset($inputJSON['produits_fournis']) ? $inputJSON['produits_fournis'] : '';
            echo json_encode(FournisseurController::addFournisseur($nomContact, $nomSociete, $telephone, $email, $produitsFournis));
            break;

        // --- UTILISATEURS & RBAC ---
        case 'get_users':
            echo json_encode(UserController::getUsers());
            break;

        case 'add_user':
            $nom = isset($inputJSON['nom']) ? $inputJSON['nom'] : '';
            $identifiant = isset($inputJSON['identifiant']) ? $inputJSON['identifiant'] : '';
            $password = isset($inputJSON['password']) ? $inputJSON['password'] : '123456';
            $role = isset($inputJSON['role']) ? $inputJSON['role'] : 'Vendeur';
            echo json_encode(UserController::addUser($nom, $identifiant, $password, $role));
            break;

        default:
            echo json_encode([
                'success' => true,
                'status' => 'online',
                'service' => 'SGC-SMARTPHONE API Mobile Home Togo',
                'version' => '1.0'
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur API: ' . $e->getMessage()]);
}
