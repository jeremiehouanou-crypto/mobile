<?php
/**
 * Caisse & Terminal de Vente (POS) Visuel & Interactif
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$erreur = isset($_GET['erreur']) ? $_GET['erreur'] : '';
$preSelectedProdId = isset($_GET['id_produit']) ? (int)$_GET['id_produit'] : 0;

// Récupérer uniquement les produits EN STOCK (> 0) depuis la BDD
$stmtP = $pdo->query("
    SELECT p.*, m.nom_marque
    FROM table_produits p
    JOIN table_marques m ON p.id_marque = m.id_marque
        WHERE COALESCE(p.quantite_stock, 1) > 0
                    AND p.type_pc <> 'À reclasser'
    ORDER BY p.nom_produit ASC
");
$produitsEnStock = $stmtP->fetchAll();

// Récupérer les clients pour l'attribution
$stmtC = $pdo->query("SELECT * FROM table_clients ORDER BY nom ASC");
$clients = $stmtC->fetchAll();

// Liste des marques pour le filtre rapide
$marques = $pdo->query("SELECT * FROM table_marques ORDER BY nom_marque ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caisse & Terminal POS | JY SHOP STORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style> 
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; } 
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-gray-800 text-sm">

    <!-- Navigation & Sidebar Réactive -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Contenu Principal -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto w-full min-w-0">
        
        <!-- En-tête de Caisse Réactif avec Hamburger Mobile -->
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 flex-shrink-0 sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <!-- Bouton Menu Mobile Hamburger -->
                <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-xl text-2xl focus:outline-none transition">
                    <i class='bx bx-menu'></i>
                </button>
                <i class='bx bx-laptop text-2xl sm:text-3xl text-emerald-600 hidden sm:inline-block'></i>
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-gray-900 leading-tight">Terminal POS & Encaissement</h1>
                    <p class="text-xs text-gray-500 hidden sm:block">Recherche de PC par génération, processeur ou carte graphique et facturation immédiate.</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <span class="px-3 sm:px-4 py-1.5 sm:py-2 bg-emerald-50 text-emerald-700 font-extrabold rounded-xl text-xs flex items-center border border-emerald-200 shadow-sm">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2 animate-ping"></span> <span class="hidden sm:inline">Caisse </span>Active
                </span>
            </div>
        </header>

        <div class="p-4 sm:p-8 space-y-6 flex-1">

            <?php if (!empty($erreur)): ?>
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-bold flex items-center">
                    <i class='bx bx-error-circle text-xl mr-3'></i>
                    <span><?php echo htmlspecialchars($erreur); ?></span>
                </div>
            <?php endif; ?>

            <!-- Interface POS 2 Colonnes -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                
                <!-- COLONNE GAUCHE : RECHERCHE VISUELLE & SÉLECTION DES PC (7 Colonnes) -->
                <div class="lg:col-span-7 space-y-5">
                    
                    <!-- Barre de Recherche Produits & Filtres Marque -->
                    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 space-y-4">
                        <div class="relative w-full">
                            <i class='bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl'></i>
                            <input type="text" id="pos-search-input" onkeyup="filtrerProduitsPOS()" placeholder="Rechercher HP, génération, processeur ou carte graphique..." 
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                        </div>

                        <!-- Filtres par marque -->
                        <div class="flex items-center space-x-2 overflow-x-auto pb-1 custom-scrollbar">
                            <button onclick="filtrerMarquePOS('tous')" class="btn-marque-filter px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm transition flex-shrink-0">
                                Tous (<?php echo count($produitsEnStock); ?>)
                            </button>
                            <?php foreach ($marques as $m): ?>
                                <button onclick="filtrerMarquePOS('<?php echo strtolower($m['nom_marque']); ?>')" class="btn-marque-filter px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition flex-shrink-0">
                                    <?php echo htmlspecialchars($m['nom_marque']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Grille des Cartes Produits Visuelles -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 max-h-[620px] overflow-y-auto pr-1 custom-scrollbar" id="grid-produits-pos">
                        <?php foreach ($produitsEnStock as $p): 
                            $nomProd   = $p['nom_produit'] ?? $p['modele_exact'] ?? 'PC portable';
                            $nomMarque = $p['nom_marque'] ?? 'Marque';
                            $stock     = (int)($p['quantite_stock'] ?? 0);
                            $prixV     = (float)($p['prix_vente'] ?? 0);
                            $ram       = $p['stockage_ram'] ?? 'RAM / SSD';
                            $generationPc = (int)($p['generation'] ?? 0);
                            $processeurPc = $p['processeur'] ?? '';
                            $carteGraphiquePc = $p['carte_graphique'] ?? '';
                            $typePc = $p['type_pc'] ?? 'Bureautique';
                            $specsPc = ($generationPc ? $generationPc . 'e génération' : $typePc) . ' • ' . $processeurPc . ' • ' . $carteGraphiquePc;
                            $img       = $p['image_url'] ?? '';
                            $imgPath   = (file_exists(__DIR__ . '/uploads/' . $img) && !empty($img)) ? 'uploads/' . $img : '';
                        ?>
                            <div class="pos-item-card bg-white rounded-2xl border-2 border-gray-100 hover:border-emerald-500 shadow-sm p-4 sm:p-5 cursor-pointer transition-all duration-200 flex flex-col justify-between group relative"
                                 id="card-prod-<?php echo $p['id_produit']; ?>"
                                 data-id="<?php echo $p['id_produit']; ?>"
                                 data-nom="<?php echo htmlspecialchars($nomProd); ?>"
                                 data-marque="<?php echo strtolower($nomMarque); ?>"
                                 data-prix="<?php echo $prixV; ?>"
                                 data-stock="<?php echo $stock; ?>"
                                 data-img="<?php echo $imgPath; ?>"
                                 data-ram="<?php echo htmlspecialchars($ram); ?>"
                                 data-search="<?php echo htmlspecialchars(strtolower($nomProd . ' ' . $nomMarque . ' ' . $ram . ' ' . $specsPc)); ?>"
                                 onclick="ajouterAuPanier(<?php echo $p['id_produit']; ?>)">

                                <!-- Badge quantité panier -->
                                <div class="badge-selected hidden absolute top-4 right-4 bg-emerald-600 text-white text-xs font-black px-2.5 py-1 rounded-full shadow-md z-10 flex items-center space-x-1">
                                    <i class='bx bx-shopping-bag text-sm'></i>
                                    <span class="qty-num">0</span>
                                </div>

                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="w-16 sm:w-20 h-16 sm:h-20 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center p-1.5 flex-shrink-0 group-hover:scale-105 transition relative group/img cursor-pointer"
                                         onclick="event.stopPropagation(); ouvrirModalImagePos('<?php echo $imgPath; ?>', '<?php echo htmlspecialchars($nomProd, ENT_QUOTES); ?>', '<?php echo htmlspecialchars($nomMarque, ENT_QUOTES); ?>', '<?php echo number_format($prixV, 0, ',', ' '); ?> FCFA', '<?php echo htmlspecialchars($ram . ' • ' . $specsPc, ENT_QUOTES); ?>')"
                                         title="Cliquer pour agrandir la photo">
                                        <?php if ($imgPath): ?>
                                            <img src="<?php echo $imgPath; ?>" alt="<?php echo htmlspecialchars($nomProd); ?>" class="max-h-full max-w-full object-contain">
                                        <?php else: ?>
                                            <i class='bx bx-laptop text-4xl text-slate-400'></i>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-indigo-900/40 rounded-xl opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center text-white text-xs">
                                            <i class='bx bx-search-alt text-base'></i>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0 space-y-0.5">
                                        <span class="text-xs font-extrabold text-indigo-600 uppercase tracking-wider block truncate">
                                            <?php echo htmlspecialchars($nomMarque); ?> • <?php echo htmlspecialchars($generationPc ? $generationPc . 'e gen' : $typePc); ?>
                                        </span>
                                        <h4 class="text-sm font-extrabold text-gray-900 truncate leading-snug">
                                            <?php echo htmlspecialchars($nomProd); ?>
                                        </h4>
                                        <span class="text-xs font-medium text-gray-500 block truncate">
                                            <?php echo htmlspecialchars($ram); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <span class="text-sm font-black text-emerald-700">
                                        <?php echo number_format($prixV, 0, ',', ' '); ?> FCFA
                                    </span>
                                    <?php if ($stock <= 3): ?>
                                        <span class="px-2.5 py-1 bg-red-100 text-red-700 font-extrabold text-xs rounded-lg">
                                            Stock: <?php echo $stock; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 font-extrabold text-xs rounded-lg">
                                            Stock: <?php echo $stock; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>


                <!-- COLONNE DROITE : PANIER & RECHERCHE CLIENT & ENCAISSEMENT (5 Colonnes) -->
                <div class="lg:col-span-5 space-y-5 sticky top-24">
                    
                    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 sm:p-6 space-y-5">
                        
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                                <i class='bx bx-shopping-bag text-emerald-600 text-2xl mr-2.5'></i> Panier (<span id="cart-count">0</span>)
                            </h3>
                            <button type="button" onclick="viderPanier()" id="btn-vider-panier" class="text-xs text-red-500 hover:text-red-700 font-bold flex items-center hidden transition">
                                <i class='bx bx-trash mr-1 text-sm'></i> Vider le panier
                            </button>
                        </div>

                        <form action="traitement.php" method="POST" id="form-vente-pos" class="space-y-5">
                            <input type="hidden" name="action" value="valider_vente">
                            <input type="hidden" name="panier_json" id="input-panier-json" value="[]">
                            <input type="hidden" name="id_client" id="input-id-client" value="0">

                            <!-- BLOC 1 : LISTE DES PC DU PANIER -->
                            <div class="space-y-2">
                                <label class="font-extrabold text-gray-700 block uppercase tracking-wider text-xs">
                                    1. Articles Sélectionnés :
                                </label>
                                
                                <div id="empty-cart-state" class="p-6 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50 space-y-2">
                                    <i class='bx bx-cart text-4xl text-gray-300'></i>
                                    <p class="text-xs font-extrabold text-gray-500 uppercase">Panier Vide</p>
                                    <p class="text-[11px] text-gray-400">Cliquez sur un produit à gauche pour l'ajouter</p>
                                </div>

                                <div id="panier-items-container" class="max-h-[280px] overflow-y-auto space-y-2.5 pr-1 custom-scrollbar"></div>
                            </div>

                            <!-- BLOC 2 : RECHERCHE & ATTRIBUTION CLIENT -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label class="font-extrabold text-gray-700 block uppercase tracking-wider text-xs">
                                        2. Client Attribué :
                                    </label>
                                    <button id="btn-choisir-client" type="button" onclick="ouvrirmodalClient()" class="text-indigo-600 hover:underline font-bold text-xs flex items-center">
                                        <i class='bx bx-user-plus mr-1 text-sm'></i> Choisir / Rechercher
                                    </button>
                                </div>

                                <div id="client-selected-display" class="p-3.5 bg-emerald-50/50 border border-emerald-200 rounded-xl flex items-center justify-between">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                                            <i class='bx bx-user'></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h5 id="sel-client-name" class="font-bold text-emerald-950 text-sm truncate">Client de Passage (Anonyme)</h5>
                                            <span id="sel-client-phone" class="text-xs text-emerald-700 font-medium truncate block">Vente directe au comptoir</span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="resetClientSelection()" class="text-gray-400 hover:text-red-500 text-lg flex-shrink-0 ml-2">
                                        <i class='bx bx-refresh'></i>
                                    </button>
                                </div>
                            </div>

                            <!-- BLOC 3 : MODE D'ENCAISSEMENT -->
                            <div class="space-y-2">
                                <label class="font-extrabold text-gray-700 block uppercase tracking-wider text-xs">
                                    3. Mode de Règlement :
                                </label>
                                
                                <div id="modes-paiement-caisse" class="grid grid-cols-2 gap-2 text-xs">
                                    <label class="label-paiement p-3 border-2 border-emerald-500 bg-emerald-50/60 rounded-xl flex items-center justify-center font-bold text-emerald-800 cursor-pointer transition">
                                        <input type="radio" name="mode_paiement" value="Espèces" checked onchange="updatePaiementUI(this)" class="hidden">
                                        <i class='bx bx-money text-lg mr-1.5'></i> Espèces
                                    </label>
                                    <label class="label-paiement p-3 border-2 border-gray-200 bg-white rounded-xl flex items-center justify-center font-bold text-gray-600 hover:bg-gray-50 cursor-pointer transition">
                                        <input type="radio" name="mode_paiement" value="T-Money" onchange="updatePaiementUI(this)" class="hidden">
                                        <i class='bx bx-mobile-alt text-lg mr-1.5'></i> T-Money
                                    </label>
                                    <label class="label-paiement p-3 border-2 border-gray-200 bg-white rounded-xl flex items-center justify-center font-bold text-gray-600 hover:bg-gray-50 cursor-pointer transition">
                                        <input type="radio" name="mode_paiement" value="Flooz" onchange="updatePaiementUI(this)" class="hidden">
                                        <i class='bx bx-mobile text-lg mr-1.5'></i> Flooz
                                    </label>
                                    <label class="label-paiement p-3 border-2 border-gray-200 bg-white rounded-xl flex items-center justify-center font-bold text-gray-600 hover:bg-gray-50 cursor-pointer transition">
                                        <input type="radio" name="mode_paiement" value="Carte Bancaire" onchange="updatePaiementUI(this)" class="hidden">
                                        <i class='bx bx-credit-card text-lg mr-1.5'></i> Carte CB
                                    </label>
                                </div>
                            </div>

                            <!-- RECAPITULATIF TOTAL -->
                            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-5 rounded-2xl text-white shadow-lg space-y-1">
                                <span class="text-xs font-extrabold uppercase tracking-wider opacity-85 block">Total TTC à Encaisser :</span>
                                <div class="text-2xl sm:text-3xl font-black" id="pos-total-display">0 F CFA</div>
                            </div>

                            <button type="submit" id="btn-valider-pos" disabled class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-extrabold rounded-xl shadow-lg transition text-xs uppercase tracking-wider flex items-center justify-center">
                                <i class='bx bx-check-circle text-xl mr-2'></i> Valider la Vente & Imprimer Facture
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- MODAL DE RECHERCHE & SÉLECTION CLIENT -->
    <div id="modal-client" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-lg w-full p-6 space-y-5 animate-in fade-in zoom-in duration-200">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                    <i class='bx bx-user-check text-indigo-600 text-xl mr-2'></i> Sélectionner un Client
                </h3>
                <button onclick="fermerModalClient()" class="text-gray-400 hover:text-gray-600 text-xl"><i class='bx bx-x'></i></button>
            </div>

            <div class="relative">
                <i class='bx bx-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-lg'></i>
                <input type="text" id="search-client-input" onkeyup="filtrerClientsModal()" placeholder="Rechercher par nom, prénom ou téléphone (+228)..." 
                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
            </div>

            <div onclick="selectionnerClientModal(0, 'Client de Passage (Anonyme)', 'Vente directe au comptoir')" 
                 class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100/50 cursor-pointer flex items-center justify-between transition">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm"><i class='bx bx-user-x'></i></div>
                    <div>
                        <h4 class="font-extrabold text-emerald-950 text-sm">Client de Passage (Anonyme)</h4>
                        <span class="text-xs text-emerald-700">Aucune fiche enregistrée</span>
                    </div>
                </div>
                <span class="px-3 py-1.5 bg-emerald-600 text-white font-bold text-xs rounded-xl">Sélectionner</span>
            </div>

            <div class="max-h-64 overflow-y-auto space-y-2.5 pr-1 custom-scrollbar" id="list-clients-modal">
                <?php foreach ($clients as $c): 
                    $nomComplet = $c['nom'] . ' ' . ($c['prenom'] ?? '');
                    $tel        = $c['telephone'] ?? '';
                    $statutFid  = $c['statut_fidelite'] ?? 'Standard';
                ?>
                    <div class="item-client-modal p-3.5 rounded-xl border border-gray-100 hover:border-indigo-500 hover:bg-indigo-50/30 cursor-pointer transition flex items-center justify-between"
                         data-id="<?php echo $c['id_client']; ?>"
                         data-nom="<?php echo htmlspecialchars($nomComplet); ?>"
                         data-tel="<?php echo htmlspecialchars($tel); ?>"
                         data-search="<?php echo strtolower($nomComplet . ' ' . $tel); ?>"
                         onclick="selectionnerClientModal(<?php echo $c['id_client']; ?>, '<?php echo addslashes($nomComplet); ?>', '<?php echo addslashes($tel); ?>')">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                <?php echo strtoupper(substr($c['nom'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-900 text-sm"><?php echo htmlspecialchars($nomComplet); ?></h4>
                                <span class="text-xs text-gray-500 font-semibold"><?php echo htmlspecialchars($tel); ?> • <?php echo htmlspecialchars($c['localisation'] ?? 'Lomé'); ?></span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 font-bold text-xs rounded-lg"><?php echo htmlspecialchars($statutFid); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Modal Agrandissement Photo Produit POS -->
    <div id="modal-img-pos-container" onclick="if(event.target.id==='modal-img-pos-container') fermerModalImagePos();" class="hidden fixed inset-0 z-50 bg-gray-950/80 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300">
        <div id="modal-img-pos-card" class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-2xl overflow-hidden flex flex-col relative transform transition-all duration-300 scale-95 opacity-0">
            <button onclick="fermerModalImagePos()" class="absolute top-4 right-4 z-20 w-10 h-10 bg-gray-900/40 hover:bg-gray-900/80 text-white rounded-full flex items-center justify-center backdrop-blur-xs transition shadow-lg text-2xl font-bold">
                <i class='bx bx-x'></i>
            </button>
            <div class="bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-950 p-6 sm:p-8 flex items-center justify-center relative min-h-[300px]">
                <img id="pos-img-modal-src" src="" alt="Photo du PC" class="hidden max-h-[340px] w-auto object-contain drop-shadow-2xl">
                <i id="pos-img-modal-placeholder" class="bx bx-laptop text-8xl text-white/60"></i>
            </div>
            <div class="p-6 bg-white flex flex-col justify-between space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <span id="pos-img-modal-marque" class="px-3 py-1 bg-indigo-600 text-white text-xs font-black rounded-lg uppercase tracking-wide">MARQUE</span>
                        <h3 id="pos-img-modal-nom" class="text-xl font-extrabold text-gray-900 mt-2">Nom Produit</h3>
                        <p id="pos-img-modal-ram" class="text-xs font-medium text-gray-500">RAM / Stockage</p>
                    </div>
                    <div id="pos-img-modal-prix" class="text-2xl font-black text-emerald-700">0 FCFA</div>
                </div>
                <button onclick="fermerModalImagePos()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-extrabold text-xs rounded-xl transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <!-- SCRIPT JAVASCRIPT POS EN TEMPS RÉEL (PANIER MULTI-PRODUITS) -->
    <script>
        let cart = [];

        window.addEventListener('DOMContentLoaded', () => {
            const preId = <?php echo $preSelectedProdId; ?>;
            if (preId > 0) {
                ajouterAuPanier(preId);
            }
        });

        function ajouterAuPanier(id) {
            const card = document.getElementById('card-prod-' + id);
            if (!card) return;

            const nom = card.dataset.nom;
            const marque = card.dataset.marque;
            const prix = parseFloat(card.dataset.prix) || 0;
            const stock = parseInt(card.dataset.stock) || 0;
            const img = card.dataset.img;
            const ram = card.dataset.ram || '';

            const existingIndex = cart.findIndex(item => item.id === id);
            if (existingIndex > -1) {
                if (cart[existingIndex].qty + 1 > stock) {
                    alert('Stock insuffisant ! Stock disponible: ' + stock);
                    return;
                }
                cart[existingIndex].qty += 1;
            } else {
                if (stock < 1) {
                    alert('Ce produit est en rupture de stock !');
                    return;
                }
                cart.push({
                    id: id,
                    nom: nom,
                    marque: marque,
                    prix: prix,
                    stock: stock,
                    img: img,
                    ram: ram,
                    qty: 1
                });
            }

            renderPanier();
        }

        function modifierQuantitePanier(id, delta) {
            const item = cart.find(i => i.id === id);
            if (!item) return;

            const newQty = item.qty + delta;
            if (newQty <= 0) {
                supprimerDuPanier(id);
                return;
            }

            if (newQty > item.stock) {
                alert('Stock insuffisant ! Stock disponible: ' + item.stock);
                return;
            }

            item.qty = newQty;
            renderPanier();
        }

        function supprimerDuPanier(id) {
            cart = cart.filter(item => item.id !== id);
            renderPanier();
        }

        function viderPanier() {
            if (cart.length > 0) {
                if (confirm('Voulez-vous vraiment vider l\'intégralité du panier ?')) {
                    cart = [];
                    renderPanier();
                }
            }
        }

        function renderPanier() {
            const container = document.getElementById('panier-items-container');
            const emptyState = document.getElementById('empty-cart-state');
            const cartCountEl = document.getElementById('cart-count');
            const totalDisplayEl = document.getElementById('pos-total-display');
            const btnValider = document.getElementById('btn-valider-pos');
            const btnVider = document.getElementById('btn-vider-panier');
            const inputPanierJson = document.getElementById('input-panier-json');

            // Mise à jour des cartes produits à gauche (Badges & bordures)
            document.querySelectorAll('.pos-item-card').forEach(card => {
                const prodId = parseInt(card.dataset.id);
                const cartItem = cart.find(i => i.id === prodId);
                const badge = card.querySelector('.badge-selected');
                
                if (cartItem) {
                    card.classList.remove('border-gray-100');
                    card.classList.add('border-emerald-600', 'bg-emerald-50/40', 'shadow-md');
                    if (badge) {
                        badge.classList.remove('hidden');
                        badge.querySelector('.qty-num').innerText = cartItem.qty;
                    }
                } else {
                    card.classList.remove('border-emerald-600', 'bg-emerald-50/40', 'shadow-md');
                    card.classList.add('border-gray-100');
                    if (badge) badge.classList.add('hidden');
                }
            });

            let totalTTC = 0;
            let totalArticles = 0;
            container.innerHTML = '';

            if (cart.length === 0) {
                emptyState.classList.remove('hidden');
                btnValider.disabled = true;
                if (btnVider) btnVider.classList.add('hidden');
                totalDisplayEl.innerText = '0 F CFA';
                cartCountEl.innerText = '0';
                inputPanierJson.value = '[]';
                return;
            }

            emptyState.classList.add('hidden');
            btnValider.disabled = false;
            if (btnVider) btnVider.classList.remove('hidden');

            cart.forEach(item => {
                const totalLigne = item.prix * item.qty;
                totalTTC += totalLigne;
                totalArticles += item.qty;

                const itemHtml = `
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between space-x-2 transition hover:border-emerald-300">
                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                            <div class="w-10 h-10 bg-white rounded-lg border border-gray-200 p-1 flex items-center justify-center flex-shrink-0">
                                <img src="${item.img}" class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-extrabold text-gray-900 text-xs truncate">${escapeHtml(item.nom)}</h4>
                                <span class="text-[11px] text-gray-500 block truncate">${escapeHtml(item.ram)} • ${new Intl.NumberFormat('fr-FR').format(item.prix)} F</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <div class="flex items-center bg-white border border-gray-200 rounded-lg overflow-hidden shadow-xs">
                                <button type="button" onclick="modifierQuantitePanier(${item.id}, -1)" class="w-6 h-6 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold flex items-center justify-center text-xs transition">-</button>
                                <span class="w-7 text-center text-xs font-black text-gray-900">${item.qty}</span>
                                <button type="button" onclick="modifierQuantitePanier(${item.id}, 1)" class="w-6 h-6 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold flex items-center justify-center text-xs transition">+</button>
                            </div>
                            
                            <span class="text-xs font-black text-emerald-700 min-w-[70px] text-right">${new Intl.NumberFormat('fr-FR').format(totalLigne)} F</span>

                            <button type="button" onclick="supprimerDuPanier(${item.id})" class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Supprimer du panier">
                                <i class='bx bx-trash text-base'></i>
                            </button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });

            cartCountEl.innerText = totalArticles;
            totalDisplayEl.innerText = new Intl.NumberFormat('fr-FR').format(totalTTC) + ' F CFA';

            const payload = cart.map(i => ({ id_produit: i.id, quantite: i.qty }));
            inputPanierJson.value = JSON.stringify(payload);
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function updatePaiementUI(radio) {
            document.querySelectorAll('.label-paiement').forEach(lbl => {
                lbl.className = "label-paiement p-3 border-2 border-gray-200 bg-white rounded-xl flex items-center justify-center font-bold text-gray-600 hover:bg-gray-50 cursor-pointer transition";
            });
            radio.parentElement.className = "label-paiement p-3 border-2 border-emerald-500 bg-emerald-50/60 rounded-xl flex items-center justify-center font-bold text-emerald-800 cursor-pointer transition";
        }

        function ouvrirmodalClient() {
            document.getElementById('modal-client').classList.remove('hidden');
        }

        function fermerModalClient() {
            document.getElementById('modal-client').classList.add('hidden');
        }

        function selectionnerClientModal(id, nom, tel) {
            document.getElementById('input-id-client').value = id;
            document.getElementById('sel-client-name').innerText = nom;
            document.getElementById('sel-client-phone').innerText = tel;
            fermerModalClient();
        }

        function resetClientSelection() {
            selectionnerClientModal(0, 'Client de Passage (Anonyme)', 'Vente directe au comptoir');
        }

        function filtrerProduitsPOS() {
            const query = document.getElementById('pos-search-input').value.toLowerCase();
            document.querySelectorAll('.pos-item-card').forEach(card => {
                if (card.dataset.search.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filtrerMarquePOS(marque) {
            const buttons = document.querySelectorAll('.btn-marque-filter');
            buttons.forEach(b => {
                b.className = "btn-marque-filter px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition flex-shrink-0";
            });
            event.target.className = "btn-marque-filter px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm transition flex-shrink-0";

            document.querySelectorAll('.pos-item-card').forEach(card => {
                if (marque === 'tous' || card.dataset.marque.includes(marque)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function ouvrirModalImagePos(img, nom, marque, prix, ram) {
            const modalImage = document.getElementById('pos-img-modal-src');
            const modalPlaceholder = document.getElementById('pos-img-modal-placeholder');
            if (img) {
                modalImage.src = img;
                modalImage.classList.remove('hidden');
                modalPlaceholder.classList.add('hidden');
            } else {
                modalImage.removeAttribute('src');
                modalImage.classList.add('hidden');
                modalPlaceholder.classList.remove('hidden');
            }
            document.getElementById('pos-img-modal-nom').textContent = nom || 'PC portable';
            document.getElementById('pos-img-modal-marque').textContent = marque || 'Marque';
            document.getElementById('pos-img-modal-prix').textContent = prix || '0 FCFA';
            document.getElementById('pos-img-modal-ram').textContent = ram || '';

            const container = document.getElementById('modal-img-pos-container');
            const card = document.getElementById('modal-img-pos-card');
            
            container.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 20);
        }

        function fermerModalImagePos() {
            const container = document.getElementById('modal-img-pos-container');
            const card = document.getElementById('modal-img-pos-card');
            if (!card) return;
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                container.classList.add('hidden');
            }, 200);
        }

        function filtrerClientsModal() {
            const query = document.getElementById('search-client-input').value.toLowerCase();
            document.querySelectorAll('.item-client-modal').forEach(item => {
                if (item.dataset.search.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>

