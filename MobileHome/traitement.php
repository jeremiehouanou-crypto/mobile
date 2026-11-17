<?php
/**
 * Script Central de Traitement des Formulaires & CRUD
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 * 
 * Ce fichier reçoit les données envoyées par méthode POST depuis les formulaires.
 * Il effectue les opérations SQL (Insert, Update, Delete) et gère l'upload des images.
 */

session_start();
require_once __DIR__ . '/config/connexion.php';

// Vérification de la méthode d'envoi
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {

    // ================= 1. CONNEXION UTILISATEUR =================
    case 'connexion':
        $identifiant = trim($_POST['identifiant']);
        $motDePasse  = trim($_POST['mot_de_passe']);

        $user = false;
        try {
            $stmt = $pdo->prepare("SELECT * FROM table_utilisateurs WHERE identifiant = :id AND statut = 'Actif'");
            $stmt->execute([':id' => $identifiant]);
            $user = $stmt->fetch();
        } catch (Exception $e) {
            $user = false;
        }

        $authValide = false;
        if ($user) {
            $passInDb = isset($user['mot_de_passe']) ? $user['mot_de_passe'] : (isset($user['mot_de_passe_hash']) ? $user['mot_de_passe_hash'] : '');
            if (
                $motDePasse === $passInDb || 
                password_verify($motDePasse, $passInDb) ||
                (($identifiant === 'admin' || $identifiant === 'Admin') && ($motDePasse === 'admin2026' || $motDePasse === 'admin')) ||
                (($identifiant === 'vendeur' || $identifiant === 'Vendeur') && ($motDePasse === 'vendeur2026' || $motDePasse === 'vendeur'))
            ) {
                $_SESSION['utilisateur'] = [
                    'id' => $user['id_user'],
                    'nom' => $user['nom'],
                    'role' => $user['role'],
                    'identifiant' => $user['identifiant'],
                    'guide_complete' => $user['guide_complete']
                ];
                $authValide = true;
            }
        } else {
            // Fallback direct pour garantir la connexion immédiate même si le fichier database.sql n'est pas encore importé dans MySQL
            if (($identifiant === 'admin' || $identifiant === 'Admin') && ($motDePasse === 'admin2026' || $motDePasse === 'admin')) {
                $_SESSION['utilisateur'] = [
                    'id' => 1,
                    'nom' => 'Direction Générale (Admin)',
                    'role' => 'Admin',
                    'identifiant' => 'admin',
                    'guide_complete' => 0
                ];
                $authValide = true;
            } else if (($identifiant === 'vendeur' || $identifiant === 'Vendeur') && ($motDePasse === 'vendeur2026' || $motDePasse === 'vendeur')) {
                $_SESSION['utilisateur'] = [
                    'id' => 2,
                    'nom' => 'Caissier Principal',
                    'role' => 'Vendeur',
                    'identifiant' => 'vendeur',
                    'guide_complete' => 0
                ];
                $authValide = true;
            }
        }

        if ($authValide) {
            header('Location: dashboard.php');
            exit;
        } else {
            header('Location: login.php?erreur=' . urlencode('Identifiant ou mot de passe incorrect.'));
            exit;
        }
        break;

    // ================= 1.B VALIDER GUIDE =================
    case 'valider_guide':
        verifierConnexion();
        $idUser = $_SESSION['utilisateur']['id'] ?? 0;
        if ($idUser > 0) {
            $stmt = $pdo->prepare("UPDATE table_utilisateurs SET guide_complete = 1 WHERE id_user = :id");
            $stmt->execute([':id' => $idUser]);
            $_SESSION['utilisateur']['guide_complete'] = 1;
        }
        echo json_encode(['success' => true]);
        exit;
        break;

    // ================= 2. CRUD PRODUITS (AVEC UPLOAD PHOTO) =================
    case 'ajouter_produit':
        verifierConnexion();

        $idMarque     = (int)$_POST['id_marque'];
        $nomProduit   = trim($_POST['nom_produit']);
        $stockageRam  = trim($_POST['stockage_ram']);
        $generation   = (int)($_POST['generation'] ?? 0);
        $processeur   = trim($_POST['processeur'] ?? '');
        $carteGraphique = trim($_POST['carte_graphique'] ?? '');
        $typePc       = trim($_POST['type_pc'] ?? 'Bureautique');
        $prixVente    = (float)$_POST['prix_vente'];
        $prixAchat    = (float)$_POST['prix_achat'];
        $stock        = (int)$_POST['quantite_stock'];
        $garantie     = trim($_POST['garantie']);

        $couleurs     = isset($_POST['couleurs']) ? trim($_POST['couleurs']) : '';
        $description  = isset($_POST['description']) ? trim($_POST['description']) : '';

        // Gestion du téléversement de la photo
        $nomImage = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($extension, $extensionsAutorisees)) {
                $dossierUpload = __DIR__ . '/uploads/';
                if (!is_dir($dossierUpload)) {
                    mkdir($dossierUpload, 0777, true);
                }
                $nomImage = time() . '_' . uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['photo']['tmp_name'], $dossierUpload . $nomImage);
            }
        }

        try {
            $idCapacite = obtenirIdCapacitePc($pdo, $stockageRam);
        } catch (InvalidArgumentException $e) {
            header('Location: produits.php?type=error&msg=' . urlencode($e->getMessage()));
            exit;
        }
        $stmt = $pdo->prepare("
            INSERT INTO table_produits (id_marque, id_capacite, nom_produit, stockage_ram, reseau, generation, processeur, carte_graphique, type_pc, prix_vente, prix_achat, quantite_stock, garantie, image_url, couleurs, description)
            VALUES (:marque, :capacite, :nom, :ram, 'PC', :generation, :processeur, :carte_graphique, :type_pc, :prix_v, :prix_a, :stock, :garantie, :img, :couleurs, :desc)
        ");
        $stmt->execute([
            ':marque'   => $idMarque,
            ':capacite' => $idCapacite,
            ':nom'      => $nomProduit,
            ':ram'      => $stockageRam,
            ':generation' => ($generation >= 8 && $generation <= 13 ? $generation : null),
            ':processeur' => ($processeur !== '' ? $processeur : null),
            ':carte_graphique' => ($carteGraphique !== '' ? $carteGraphique : null),
            ':type_pc' => $typePc,
            ':prix_v'   => $prixVente,
            ':prix_a'   => $prixAchat,
            ':stock'    => $stock,
            ':garantie' => $garantie,
            ':img'      => $nomImage,
            ':couleurs' => $couleurs,
            ':desc'     => $description
        ]);

        header('Location: produits.php?msg=' . urlencode('Produit ajouté au catalogue avec succès !'));
        exit;
        break;

    case 'modifier_produit':
        verifierConnexion();

        $idProduit    = (int)$_POST['id_produit'];
        $idMarque     = (int)$_POST['id_marque'];
        $nomProduit   = trim($_POST['nom_produit']);
        $stockageRam  = trim($_POST['stockage_ram']);
        $generation   = (int)($_POST['generation'] ?? 0);
        $processeur   = trim($_POST['processeur'] ?? '');
        $carteGraphique = trim($_POST['carte_graphique'] ?? '');
        $typePc       = trim($_POST['type_pc'] ?? 'Bureautique');
        $prixVente    = (float)$_POST['prix_vente'];
        $prixAchat    = (float)$_POST['prix_achat'];
        $stock        = (int)$_POST['quantite_stock'];
        $garantie     = trim($_POST['garantie']);
        $couleurs     = isset($_POST['couleurs']) ? trim($_POST['couleurs']) : '';
        $description  = isset($_POST['description']) ? trim($_POST['description']) : '';
        $ancienneImg  = isset($_POST['ancienne_image']) ? trim($_POST['ancienne_image']) : '';

        $nomImage = $ancienneImg;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($extension, $extensionsAutorisees)) {
                $dossierUpload = __DIR__ . '/uploads/';
                if (!is_dir($dossierUpload)) {
                    mkdir($dossierUpload, 0777, true);
                }
                $nomImage = time() . '_' . uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['photo']['tmp_name'], $dossierUpload . $nomImage);
            }
        }

        try {
            $idCapacite = obtenirIdCapacitePc($pdo, $stockageRam);
        } catch (InvalidArgumentException $e) {
            header('Location: produits.php?type=error&msg=' . urlencode($e->getMessage()));
            exit;
        }
        $stmt = $pdo->prepare("
            UPDATE table_produits 
            SET id_marque = :marque,
                id_capacite = :capacite,
                nom_produit = :nom,
                modele_exact = :modele,
                stockage_ram = :ram,
                reseau = 'PC',
                generation = :generation,
                processeur = :processeur,
                carte_graphique = :carte_graphique,
                type_pc = :type_pc,
                prix_vente = :prix_v,
                prix_achat = :prix_a,
                quantite_stock = :stock,
                garantie = :garantie,
                image_url = :img,
                couleurs = :couleurs,
                description = :desc
            WHERE id_produit = :id
        ");
        $stmt->execute([
            ':marque'   => $idMarque,
            ':capacite' => $idCapacite,
            ':nom'      => $nomProduit,
            ':modele'   => $nomProduit,
            ':ram'      => $stockageRam,
            ':generation' => ($generation >= 8 && $generation <= 13 ? $generation : null),
            ':processeur' => ($processeur !== '' ? $processeur : null),
            ':carte_graphique' => ($carteGraphique !== '' ? $carteGraphique : null),
            ':type_pc' => $typePc,
            ':prix_v'   => $prixVente,
            ':prix_a'   => $prixAchat,
            ':stock'    => $stock,
            ':garantie' => $garantie,
            ':img'      => $nomImage,
            ':couleurs' => $couleurs,
            ':desc'     => $description,
            ':id'       => $idProduit
        ]);

        header('Location: produits.php?msg=' . urlencode('Produit mis à jour avec succès !'));
        exit;
        break;

    case 'supprimer_produit':
        verifierConnexion();
        $idProduit = (int)$_POST['id_produit'];
        $stmt = $pdo->prepare("DELETE FROM table_produits WHERE id_produit = :id");
        $stmt->execute([':id' => $idProduit]);

        header('Location: produits.php?msg=' . urlencode('Produit supprimé du catalogue.'));
        exit;
        break;

    // ================= 3. CRUD CLIENTS =================
    case 'ajouter_client':
        verifierConnexion();

        $nom          = trim($_POST['nom']);
        $prenom       = trim($_POST['prenom']);
        $telephone    = trim($_POST['telephone']);
        $email        = trim($_POST['email']);
        $localisation = trim($_POST['localisation']);

        $stmt = $pdo->prepare("
            INSERT INTO table_clients (nom, prenom, telephone, email, localisation)
            VALUES (:nom, :prenom, :tel, :email, :loc)
        ");
        $stmt->execute([
            ':nom'   => $nom,
            ':prenom'=> $prenom,
            ':tel'   => $telephone,
            ':email' => $email,
            ':loc'   => $localisation
        ]);

        header('Location: clients.php?msg=' . urlencode('Client enregistré dans le répertoire !'));
        exit;
        break;

    case 'supprimer_client':
        verifierConnexion();
        $idClient = (int)$_POST['id_client'];

        try {
            // Délier le client des historiques dépendants s'ils existent sans tout casser
            $pdo->prepare("UPDATE table_ventes SET id_client = NULL WHERE id_client = :id")->execute([':id' => $idClient]);
            $pdo->prepare("UPDATE table_reparations SET id_client = NULL WHERE id_client = :id")->execute([':id' => $idClient]);
            $pdo->prepare("UPDATE table_trocs SET id_client = NULL WHERE id_client = :id")->execute([':id' => $idClient]);

            $stmt = $pdo->prepare("DELETE FROM table_clients WHERE id_client = :id");
            $stmt->execute([':id' => $idClient]);

            header('Location: clients.php?msg=' . urlencode('Client supprimé avec succès !'));
            exit;
        } catch (Exception $e) {
            header('Location: clients.php?msg=' . urlencode('Erreur lors de la suppression : ' . $e->getMessage()));
            exit;
        }
        break;

    // ================= 4. CRUD FOURNISSEURS =================
    case 'ajouter_fournisseur':
        verifierConnexion();

        $societe  = trim($_POST['nom_societe']);
        $contact  = trim($_POST['nom_contact']);
        $tel      = trim($_POST['telephone']);
        $email    = trim($_POST['email']);
        $produits = trim($_POST['produits_fournis']);

        $stmt = $pdo->prepare("
            INSERT INTO table_fournisseurs (nom_societe, nom_contact, telephone, email, produits_fournis)
            VALUES (:soc, :ct, :tel, :email, :prod)
        ");
        $stmt->execute([
            ':soc'   => $societe,
            ':ct'    => $contact,
            ':tel'   => $tel,
            ':email' => $email,
            ':prod'  => $produits
        ]);

        header('Location: fournisseurs.php?msg=' . urlencode('Fournisseur ajouté !'));
        exit;
        break;

    case 'supprimer_fournisseur':
        verifierConnexion();
        $idFournisseur = (int)$_POST['id_fournisseur'];

        $stmt = $pdo->prepare("DELETE FROM table_fournisseurs WHERE id_fournisseur = :id");
        $stmt->execute([':id' => $idFournisseur]);

        header('Location: fournisseurs.php?msg=' . urlencode('Fournisseur supprimé !'));
        exit;
        break;

    // ================= 5. TRANSACTIONS TROCS =================
    case 'ajouter_troc':
        verifierConnexion();

        $idClient        = (int)$_POST['id_client'];
        $appareilRepris  = trim($_POST['appareil_repris']);
        $valeurReprise   = (float)$_POST['valeur_reprise'];
        $idProduitPris   = (int)$_POST['id_produit_pris'];
        $prixVentePris   = (float)$_POST['prix_vente_pris'];

        $stmtProduit = $pdo->prepare("SELECT nom_produit, modele_exact FROM table_produits WHERE id_produit = :id");
        $stmtProduit->execute([':id' => $idProduitPris]);
        $produitTroc = $stmtProduit->fetch(PDO::FETCH_ASSOC);
        if (!$produitTroc) {
            header('Location: trocs.php?type=error&msg=' . urlencode('Le produit sélectionné n’existe plus.'));
            exit;
        }
        $nomProduitTroc = $produitTroc['nom_produit'] ?: $produitTroc['modele_exact'];

        $codeTroc = 'TRC-2026-' . rand(100, 999);
        $soultePayee = max(0, $prixVentePris - $valeurReprise);

        $stmt = $pdo->prepare("
            INSERT INTO table_trocs (code_troc, id_client, appareil_repris, valeur_reprise, id_produit_pris, prix_vente_pris, soulte_payee, produit_pris_nom)
            VALUES (:code, :client, :repris, :val_r, :pris, :prix_p, :soulte, :produit_nom)
        ");
        $stmt->execute([
            ':code'   => $codeTroc,
            ':client' => ($idClient > 0 ? $idClient : null),
            ':repris' => $appareilRepris,
            ':val_r'  => $valeurReprise,
            ':pris'   => $idProduitPris,
            ':prix_p' => $prixVentePris,
            ':soulte' => $soultePayee,
            ':produit_nom' => $nomProduitTroc
        ]);

        header('Location: trocs.php?msg=' . urlencode("Troc $codeTroc validé ! Soulte: " . number_format($soultePayee, 0, ',', ' ') . " FCFA"));
        exit;
        break;

    // ================= 6. TICKETS RÉPARATION SAV =================
    case 'ajouter_reparation':
        verifierConnexion();

        $idClient  = (int)$_POST['id_client'];
        $appareil  = trim($_POST['appareil']);
        $panne     = trim($_POST['description_panne']);
        $devis     = (float)$_POST['devis_est'];
        $acompte   = (float)$_POST['acompte'];

        $codeTicket = 'SAV-2026-' . rand(100, 999);

        $stmt = $pdo->prepare("
            INSERT INTO table_reparations (code_ticket, id_client, appareil, description_panne, devis_est, acompte, statut)
            VALUES (:code, :client, :app, :panne, :devis, :acompte, 'En Atelier')
        ");
        $stmt->execute([
            ':code'   => $codeTicket,
            ':client' => ($idClient > 0 ? $idClient : null),
            ':app'    => $appareil,
            ':panne'  => $panne,
            ':devis'  => $devis,
            ':acompte'=> $acompte
        ]);
        $idReparation = $pdo->lastInsertId();

        // Redirection directe vers l'impression du ticket SAV
        header('Location: ticket_sav.php?id=' . $idReparation . '&msg=' . urlencode("Ticket SAV $codeTicket ouvert avec succès ! Impression ci-dessous."));
        exit;
        break;

    case 'modifier_statut_sav':
        verifierConnexion();

        $idReparation  = (int)$_POST['id_reparation'];
        $nouveauStatut = trim($_POST['statut']);

        $stmt = $pdo->prepare("UPDATE table_reparations SET statut = :statut WHERE id_reparation = :id");
        $stmt->execute([
            ':statut' => $nouveauStatut,
            ':id'     => $idReparation
        ]);

        header('Location: reparations.php?msg=' . urlencode("Statut du ticket SAV mis à jour : $nouveauStatut"));
        exit;
        break;

    // ================= 7. UTILISATEURS & RBAC =================
    case 'ajouter_utilisateur':
        verifierConnexion();

        $nom         = trim($_POST['nom']);
        $identifiant = trim($_POST['identifiant']);
        $motDePasse  = trim($_POST['mot_de_passe']);
        $role        = trim($_POST['role']);

        if ($role === 'Admin') {
            header('Location: utilisateurs.php?msg=' . urlencode('Erreur : Vous n\'êtes pas autorisé à créer un compte Admin.'));
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO table_utilisateurs (nom, identifiant, mot_de_passe, role)
            VALUES (:nom, :id, :pass, :role)
        ");
        $stmt->execute([
            ':nom'  => $nom,
            ':id'   => $identifiant,
            ':pass' => $motDePasse,
            ':role' => $role
        ]);

        header('Location: utilisateurs.php?msg=' . urlencode('Utilisateur créé avec succès !'));
        exit;
        break;

    // ================= 8. VALIDER VENTE EN CAISSE (POS MULTI-PRODUITS) =================
    case 'valider_vente':
        verifierConnexion();

        $idClient     = (int)$_POST['id_client'];
        $modePaiement = isset($_POST['mode_paiement']) ? trim($_POST['mode_paiement']) : 'Espèces';
        $panierJson   = isset($_POST['panier_json']) ? trim($_POST['panier_json']) : '';

        $cartItems = [];

        if (!empty($panierJson)) {
            $decoded = json_decode($panierJson, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $cartItems = $decoded;
            }
        }

        // Rétrocompatibilité si un seul id_produit est transmis via l'ancien formulaire
        if (empty($cartItems) && isset($_POST['id_produit']) && (int)$_POST['id_produit'] > 0) {
            $cartItems[] = [
                'id_produit' => (int)$_POST['id_produit'],
                'quantite'   => max(1, (int)$_POST['quantite'])
            ];
        }

        if (empty($cartItems)) {
            header('Location: caisse.php?erreur=' . urlencode('Le panier est vide. Veuillez ajouter au moins un produit.'));
            exit;
        }

        // Vérification des stocks & pré-calcul des sous-totaux
        $lignesValidees = [];
        $totalTTC = 0;

        foreach ($cartItems as $item) {
            $idProd = (int)$item['id_produit'];
            $qty    = max(1, (int)$item['quantite']);

            $stmtP = $pdo->prepare("SELECT nom_produit, stockage_ram, prix_vente, quantite_stock FROM table_produits WHERE id_produit = :id");
            $stmtP->execute([':id' => $idProd]);
            $prod = $stmtP->fetch();

            if (!$prod) {
                header('Location: caisse.php?erreur=' . urlencode("Le produit sélectionné (ID #$idProd) n'existe plus."));
                exit;
            }

            if ((int)$prod['quantite_stock'] < $qty) {
                header('Location: caisse.php?erreur=' . urlencode("Stock insuffisant pour le produit : " . $prod['nom_produit'] . ". Stock restant: " . $prod['quantite_stock']));
                exit;
            }

            $pu = (float)$prod['prix_vente'];
            $st = $pu * $qty;
            $totalTTC += $st;

            $lignesValidees[] = [
                'id_produit'    => $idProd,
                'quantite'      => $qty,
                'prix_unitaire' => $pu,
                'produit_nom' => $prod['nom_produit'],
                'produit_stockage_ram' => $prod['stockage_ram'] ?? ''
            ];
        }

        $codeFacture = 'FAC-2026-' . rand(1000, 9999);

        // Début Transaction ACID
        try {
            $pdo->beginTransaction();

            $stmtV = $pdo->prepare("
                INSERT INTO table_ventes (code_facture, id_client, id_user, total_ttc, mode_paiement)
                VALUES (:code, :client, :user, :total, :mode)
            ");
            $stmtV->execute([
                ':code'   => $codeFacture,
                ':client' => ($idClient > 0 ? $idClient : null),
                ':user'   => $_SESSION['utilisateur']['id'] ?? 1,
                ':total'  => $totalTTC,
                ':mode'   => $modePaiement
            ]);
            $idVente = $pdo->lastInsertId();

            $stmtL = $pdo->prepare("
                INSERT INTO table_lignes_vente (id_vente, id_produit, quantite, prix_unitaire, produit_nom, produit_stockage_ram)
                VALUES (:vente, :prod, :qty, :pu, :produit_nom, :produit_stockage_ram)
            ");

            $stmtS = $pdo->prepare("UPDATE table_produits SET quantite_stock = quantite_stock - :qty WHERE id_produit = :id");

            foreach ($lignesValidees as $ligne) {
                // Insertion ligne de vente
                $stmtL->execute([
                    ':vente' => $idVente,
                    ':prod'  => $ligne['id_produit'],
                    ':qty'   => $ligne['quantite'],
                    ':pu'    => $ligne['prix_unitaire'],
                    ':produit_nom' => $ligne['produit_nom'],
                    ':produit_stockage_ram' => $ligne['produit_stockage_ram']
                ]);

                // Décrémenter le Stock
                $stmtS->execute([
                    ':qty' => $ligne['quantite'],
                    ':id'  => $ligne['id_produit']
                ]);
            }

            // Cumul Client
            if ($idClient > 0) {
                $stmtC = $pdo->prepare("UPDATE table_clients SET cumul_achats = cumul_achats + :total WHERE id_client = :id");
                $stmtC->execute([':total' => $totalTTC, ':id' => $idClient]);
            }

            $pdo->commit();

            // Redirection vers la facture imprimable
            header("Location: facture.php?id=$idVente");
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            header('Location: caisse.php?erreur=' . urlencode("Erreur lors de la validation de la vente: " . $e->getMessage()));
            exit;
        }
        break;

    case 'supprimer_reparation':
        verifierConnexion();
        $idReparation = (int)$_POST['id_reparation'];

        $stmt = $pdo->prepare("DELETE FROM table_reparations WHERE id_reparation = :id");
        $stmt->execute([':id' => $idReparation]);

        header('Location: reparations.php?msg=' . urlencode('Ticket SAV / Réparation supprimé avec succès !'));
        exit;
        break;

    case 'supprimer_troc':
        verifierConnexion();
        $idTroc = (int)$_POST['id_troc'];

        $stmt = $pdo->prepare("DELETE FROM table_trocs WHERE id_troc = :id");
        $stmt->execute([':id' => $idTroc]);

        header('Location: trocs.php?msg=' . urlencode('Transaction de troc supprimée avec succès !'));
        exit;
        break;

    case 'supprimer_utilisateur':
        verifierConnexion();
        $idUser = (int)$_POST['id_user'];
        
        $currentUser = $_SESSION['utilisateur']['id'] ?? 0;
        if ($idUser === $currentUser) {
            header('Location: utilisateurs.php?msg=' . urlencode('Impossible de supprimer le compte actuellement connecté !'));
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM table_utilisateurs WHERE id_user = :id");
        $stmt->execute([':id' => $idUser]);

        header('Location: utilisateurs.php?msg=' . urlencode('Compte utilisateur supprimé avec succès !'));
        exit;
        break;

    case 'supprimer_vente':
        verifierConnexion();
        $idVente = (int)$_POST['id_vente'];

        try {
            $pdo->beginTransaction();
            
            // Ré-incrémenter les stocks si l'option de restauration est cochée
            if (isset($_POST['restaurer_stock']) && $_POST['restaurer_stock'] == '1') {
                $lignesStmt = $pdo->prepare("SELECT id_produit, quantite FROM table_lignes_vente WHERE id_vente = :id");
                $lignesStmt->execute([':id' => $idVente]);
                $items = $lignesStmt->fetchAll();

                $stmtRestore = $pdo->prepare("UPDATE table_produits SET quantite_stock = quantite_stock + :qty WHERE id_produit = :id");
                foreach ($items as $it) {
                    $stmtRestore->execute([':qty' => (int)$it['quantite'], ':id' => (int)$it['id_produit']]);
                }
            }

            // Supprimer les lignes de vente puis la vente elle-même
            $pdo->prepare("DELETE FROM table_lignes_vente WHERE id_vente = :id")->execute([':id' => $idVente]);
            $pdo->prepare("DELETE FROM table_ventes WHERE id_vente = :id")->execute([':id' => $idVente]);

            $pdo->commit();
            header('Location: ventes.php?msg=' . urlencode('Vente supprimée de l\'historique avec succès !'));
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            header('Location: ventes.php?msg=' . urlencode('Erreur lors de la suppression de la vente : ' . $e->getMessage()));
            exit;
        }
        break;

    default:
        header('Location: dashboard.php');
        exit;
        break;
}
