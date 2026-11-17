<?php
/**
 * Gestion du Catalogue Produits & Stocks (CRUD + Upload Photos)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$isError = isset($_GET['type']) && $_GET['type'] === 'error';
$voirAnciens = isset($_GET['anciens']) && $_GET['anciens'] === '1';
$nombreAnciens = (int)$pdo->query("SELECT COUNT(*) FROM table_produits WHERE type_pc = 'À reclasser'")->fetchColumn();

$filtreCatalogue = $voirAnciens ? "p.type_pc = 'À reclasser'" : "p.type_pc <> 'À reclasser'";
$stmt = $pdo->query("
    SELECT p.*, m.nom_marque
    FROM table_produits p
    JOIN table_marques m ON p.id_marque = m.id_marque
    WHERE $filtreCatalogue
    ORDER BY p.id_produit DESC
");
$produits = $stmt->fetchAll();

// Liste des marques pour le formulaire et le filtre
$marques = $pdo->query("SELECT * FROM table_marques ORDER BY (nom_marque = 'HP') DESC, nom_marque ASC")->fetchAll();

/**
 * Fonction d'aide pour générer des pastilles de couleur visuelles
 */
function getPastilleCouleurHex($nomCouleur) {
    $c = strtolower(trim($nomCouleur));
    if (strpos($c, 'titane') !== false || strpos($c, 'gris') !== false) return '#64748b';
    if (strpos($c, 'noir') !== false || strpos($c, 'obsidienne') !== false || strpos($c, 'minuit') !== false) return '#1e293b';
    if (strpos($c, 'désert') !== false || strpos($c, 'or') !== false || strpos($c, 'gold') !== false) return '#d4af37';
    if (strpos($c, 'argent') !== false || strpos($c, 'blanc') !== false || strpos($c, 'porcelaine') !== false) return '#e2e8f0';
    if (strpos($c, 'vert') !== false || strpos($c, 'hazel') !== false) return '#10b981';
    if (strpos($c, 'bleu') !== false || strpos($c, 'navy') !== false) return '#2563eb';
    if (strpos($c, 'violet') !== false) return '#8b5cf6';
    if (strpos($c, 'rouge') !== false) return '#ef4444';
    return '#94a3b8';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue PC | JY SHOP STORE</title>
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
        
        <!-- En-tête -->
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 flex-shrink-0 sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-xl text-2xl focus:outline-none transition">
                    <i class='bx bx-menu'></i>
                </button>
                <i class='bx bx-laptop text-2xl sm:text-3xl text-indigo-600 hidden sm:inline-block'></i>
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-gray-900 leading-tight">Catalogue des PC portables</h1>
                    <p class="text-xs text-gray-500 hidden sm:block">PC HP bureautiques et gamer, générations 8 à 13.</p>
                </div>
            </div>

            <!-- Commutateur de Vue (Grille vs Tableau) & Bouton Ajouter -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <a href="produits.php<?php echo $voirAnciens ? '' : '?anciens=1'; ?>" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition flex items-center" title="Afficher les anciens produits à reclasser">
                    <i class='bx <?php echo $voirAnciens ? 'bx-arrow-back' : 'bx-history'; ?> text-lg mr-1'></i>
                    <span class="hidden sm:inline"><?php echo $voirAnciens ? 'Catalogue PC' : 'Anciens articles'; ?></span>
                    <?php if (!$voirAnciens && $nombreAnciens > 0): ?>
                        <span class="ml-1 px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded-md"><?php echo $nombreAnciens; ?></span>
                    <?php endif; ?>
                </a>
                <div class="bg-gray-100 p-1 rounded-xl flex items-center space-x-1">
                    <button id="btn-grid" onclick="switchView('grid')" class="px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold transition flex items-center bg-white text-indigo-600 shadow-sm">
                        <i class='bx bxs-grid-alt mr-1 sm:mr-1.5 text-base'></i> <span class="hidden sm:inline">Grille</span>
                    </button>
                    <button id="btn-table" onclick="switchView('table')" class="px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold transition flex items-center text-gray-500 hover:text-gray-700">
                        <i class='bx bx-list-ul mr-1 sm:mr-1.5 text-base'></i> <span class="hidden sm:inline">Tableau</span>
                    </button>
                </div>

                <?php if (!$voirAnciens): ?>
                <button id="btn-nouveau-produit" onclick="toggleFormAjout()" class="px-3 sm:px-5 py-2 sm:py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs shadow-md transition flex items-center">
                    <i class='bx bx-plus-circle mr-1 sm:mr-2 text-base sm:text-lg'></i> <span class="hidden sm:inline">Nouveau </span>PC
                </button>
                <?php endif; ?>
            </div>
        </header>

        <div class="p-4 sm:p-8 space-y-6 flex-1">

            <!-- Message d'alerte / notification -->
            <?php if (!empty($msg)): ?>
                <div class="p-4 <?php echo $isError ? 'bg-red-50 border-red-200 text-red-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800'; ?> border rounded-xl text-sm font-bold flex items-center shadow-sm">
                    <i class='bx <?php echo $isError ? 'bx-error-circle text-red-600' : 'bx-check-circle text-emerald-600'; ?> text-2xl mr-3'></i>
                    <span><?php echo htmlspecialchars($msg); ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulaire Masquable d'Ajout de Produit -->
            <div id="form-ajout-container" class="hidden bg-white rounded-2xl shadow-md border border-gray-100 p-5 sm:p-6 space-y-5">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                        <i class='bx bx-plus-circle text-indigo-600 text-xl mr-2.5'></i> Ajouter un PC au catalogue
                    </h3>
                    <button onclick="toggleFormAjout()" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class='bx bx-x'></i>
                    </button>
                </div>
                
                <form action="traitement.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <input type="hidden" name="action" value="ajouter_produit">

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Marque :</label>
                        <select name="id_marque" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm">
                            <?php foreach ($marques as $m): ?>
                                <option value="<?php echo $m['id_marque']; ?>"><?php echo htmlspecialchars($m['nom_marque']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Modèle Exact :</label>
                        <input type="text" name="nom_produit" required placeholder="ex: HP EliteBook 840 G8" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">RAM / SSD :</label>
                        <input type="text" name="stockage_ram" required placeholder="ex: 16 Go RAM / 512 Go SSD" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Type de PC :</label>
                        <select name="type_pc" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-semibold text-sm">
                            <option value="Bureautique">Bureautique</option>
                            <option value="Gamer">PC Gamer</option>
                            <option value="Professionnel">Professionnel</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Génération :</label>
                        <select name="generation" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-semibold text-sm">
                            <option value="">Choisir (8 à 13)</option>
                            <?php for ($generation = 8; $generation <= 13; $generation++): ?>
                                <option value="<?php echo $generation; ?>"><?php echo $generation; ?>e génération</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Processeur :</label>
                        <input type="text" name="processeur" placeholder="ex: Intel Core i5-1135G7" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Carte graphique :</label>
                        <input type="text" name="carte_graphique" placeholder="ex: Intel Iris Xe / NVIDIA RTX 3050" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Prix de Vente Client (FCFA) :</label>
                        <input type="number" name="prix_vente" required placeholder="980000" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-indigo-700 text-sm outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Prix d'Achat Fournisseur (FCFA) :</label>
                        <input type="number" name="prix_achat" placeholder="850000" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-sm outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Quantité Stock Initial :</label>
                        <input type="number" name="quantite_stock" required placeholder="10" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Garantie :</label>
                        <input type="text" name="garantie" placeholder="ex: 3 mois" value="3 mois" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="font-bold text-gray-700 block mb-1">Photo Officielle du Produit :</label>
                        <input type="file" name="photo" accept="image/*" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 text-xs">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4">
                        <label class="font-bold text-gray-700 block mb-1">Description :</label>
                        <textarea name="description" rows="2" placeholder="État, taille d’écran, autonomie, clavier, accessoires inclus…" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none"></textarea>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 pt-2">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center justify-center">
                            <i class='bx bx-check-circle mr-2 text-lg'></i> Enregistrer & Téléverser au Catalogue
                        </button>
                    </div>
                </form>
            </div>

            <!-- Filtres par Marque & Barre de Recherche -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 flex flex-col md:flex-row items-center justify-between gap-4">
                
                <!-- Filtres Marque -->
                <div class="flex items-center space-x-2.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 custom-scrollbar">
                            <button onclick="filtrerMarque('tous')" class="btn-filter px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-sm transition flex-shrink-0">
                        Tous (<?php echo count($produits); ?>)
                    </button>
                    <?php foreach ($marques as $m): ?>
                        <button onclick="filtrerMarque('<?php echo strtolower($m['nom_marque']); ?>')" class="btn-filter px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition flex-shrink-0">
                            <?php echo htmlspecialchars($m['nom_marque']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Recherche directe -->
                <div class="relative w-full md:w-72">
                    <i class='bx bx-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg'></i>
                    <input type="text" id="input-recherche-produit" onkeyup="filtrerRecherche()" placeholder="Rechercher un modèle..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
            </div>

            <!-- VUE 1 : GRILLE DES PC -->
            <div id="view-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php 
                $idxCard = 0;
                foreach ($produits as $p): 
                    $idxCard++;
                    $nomProduit   = $p['nom_produit'] ?? $p['modele_exact'] ?? 'PC portable';
                    $nomMarque    = $p['nom_marque'] ?? 'Marque';
                    $stockageRam  = $p['stockage_ram'] ?? 'RAM / SSD non renseignés';
                    $generationPc = (int)($p['generation'] ?? 0);
                    $processeurPc = $p['processeur'] ?? '';
                    $carteGraphiquePc = $p['carte_graphique'] ?? '';
                    $typePc       = $p['type_pc'] ?? 'Bureautique';
                    $stock        = (int)($p['quantite_stock'] ?? 0);
                    $prixVente    = (float)($p['prix_vente'] ?? 0);
                    $prixAchat    = (float)($p['prix_achat'] ?? 0);
                    $garantie     = $p['garantie'] ?? '12 Mois';
                    $description  = !empty($p['description']) ? $p['description'] : 'Ordinateur portable HP disponible en boutique.';
                    $marge        = $prixVente - $prixAchat;
                ?>
                    <div class="card-produit bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group overflow-hidden"
                         data-marque="<?php echo strtolower($nomMarque); ?>"
                         data-nom="<?php echo strtolower($nomProduit . ' ' . $nomMarque . ' ' . $stockageRam . ' ' . $processeurPc . ' ' . $carteGraphiquePc . ' ' . $generationPc . 'e generation ' . $typePc); ?>">
                        
                        <!-- Image mise en avant & Badges -->
                        <?php 
                        $imgSrcGrid = (file_exists(__DIR__ . '/uploads/' . $p['image_url']) && !empty($p['image_url'])) 
                            ? 'uploads/' . htmlspecialchars($p['image_url']) 
                            : '';
                        $dataJsonGrid = json_encode([
                            'img' => $imgSrcGrid,
                            'nom' => $nomProduit,
                            'marque' => $nomMarque,
                            'ram' => $stockageRam,
                            'generation' => $generationPc,
                            'processeur' => $processeurPc,
                            'carte_graphique' => $carteGraphiquePc,
                            'type_pc' => $typePc,
                            'prix' => number_format($prixVente, 0, ',', ' ') . ' FCFA',
                            'stock' => $stock,
                            'garantie' => $garantie,
                            'desc' => $description
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                        ?>
                        <div class="h-56 sm:h-60 bg-gradient-to-b from-gray-50 via-gray-50/50 to-white flex items-center justify-center p-5 relative overflow-hidden group/img cursor-pointer"
                             onclick='ouvrirModalImage(<?php echo $dataJsonGrid; ?>)'
                             title="Cliquer pour agrandir la photo">
                            
                            <!-- Badge Stock (Top Left) -->
                            <div class="absolute top-4 left-4 z-10 pointer-events-none">
                                <?php if ($stock <= 3): ?>
                                    <span class="px-3 py-1 bg-red-600 text-white font-extrabold text-xs rounded-full shadow-md flex items-center">
                                        <i class='bx bx-error-circle mr-1 text-sm'></i> Stock: <?php echo $stock; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-emerald-600 text-white font-extrabold text-xs rounded-full shadow-md flex items-center">
                                        <i class='bx bx-check-double mr-1 text-sm'></i> Stock: <?php echo $stock; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Badge Marque et génération -->
                            <div class="absolute top-4 right-4 z-10 flex flex-col items-end space-y-1 pointer-events-none">
                                <span class="px-3 py-1 bg-indigo-600 text-white text-xs font-extrabold rounded-lg shadow-sm">
                                    <?php echo htmlspecialchars($nomMarque); ?>
                                </span>
                                <span class="px-2.5 py-0.5 bg-gray-800 text-white text-[10px] font-bold rounded-md">
                                    <?php echo $generationPc ? $generationPc . 'e génération' : htmlspecialchars($typePc); ?>
                                </span>
                            </div>

                            <!-- Overlay au survol "Agrandir" -->
                            <div class="absolute inset-0 bg-indigo-950/20 opacity-0 group-hover/img:opacity-100 transition-opacity duration-200 flex items-center justify-center z-10 backdrop-blur-[2px]">
                                <span class="bg-white/90 text-indigo-700 px-3.5 py-1.5 rounded-full font-extrabold text-xs shadow-lg flex items-center transform translate-y-2 group-hover/img:translate-y-0 transition-transform">
                                    <i class='bx bx-search-alt text-base mr-1.5'></i> Agrandir la photo
                                </span>
                            </div>

                            <!-- Visuel Photo Produit -->
                            <?php if (file_exists(__DIR__ . '/uploads/' . $p['image_url']) && !empty($p['image_url'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($nomProduit); ?>" class="h-44 sm:h-48 object-contain group-hover/img:scale-110 transition duration-300 drop-shadow-md">
                            <?php else: ?>
                                <span class="text-slate-400 flex flex-col items-center"><i class='bx bx-laptop text-8xl'></i><span class="text-xs font-bold">PC portable</span></span>
                            <?php endif; ?>
                        </div>

                        <!-- Corps de la Carte -->
                        <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                            
                            <div class="space-y-3">
                                <h3 class="text-base sm:text-lg font-extrabold text-gray-900 leading-snug group-hover:text-indigo-600 transition">
                                    <?php echo htmlspecialchars($nomProduit); ?>
                                </h3>

                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                    <?php echo htmlspecialchars($description); ?>
                                </p>

                                <div class="grid grid-cols-2 gap-2 pt-1 text-xs">
                                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 flex items-center font-semibold text-gray-700 min-w-0">
                                        <i class='bx bx-memory-card text-indigo-500 text-base mr-2 flex-shrink-0'></i>
                                        <span class="truncate"><?php echo htmlspecialchars($stockageRam); ?></span>
                                    </div>
                                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 flex items-center font-semibold text-gray-700 min-w-0">
                                        <i class='bx bx-chip text-emerald-500 text-base mr-2 flex-shrink-0'></i>
                                        <span class="truncate"><?php echo htmlspecialchars($processeurPc ?: 'Processeur à préciser'); ?></span>
                                    </div>
                                    <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-100 flex items-center font-semibold text-gray-700 min-w-0 col-span-2">
                                        <i class='bx bx-joystick text-amber-500 text-base mr-2 flex-shrink-0'></i>
                                        <span class="truncate"><?php echo htmlspecialchars($carteGraphiquePc ?: $typePc); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-gray-400 font-bold block uppercase">Prix Vente</span>
                                    <span class="text-lg sm:text-xl font-black text-indigo-700 leading-none">
                                        <?php echo number_format($prixVente, 0, ',', ' '); ?> <span class="text-xs">FCFA</span>
                                    </span>
                                </div>

                                <div class="flex items-center space-x-1.5">
                                    <a <?php echo ($idxCard === 1) ? 'id="btn-vendre-produit"' : ''; ?> href="caisse.php?id_produit=<?php echo $p['id_produit']; ?>" title="Vendre au POS" class="p-2 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-xl font-bold transition shadow-sm flex items-center text-xs">
                                        <i class='bx bx-cart text-lg'></i>
                                    </a>
                                    <button <?php echo ($idxCard === 1) ? 'id="btn-editer-produit"' : ''; ?> onclick='ouvrirModalEdition(<?php echo json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' title="Modifier le produit" class="p-2 bg-amber-50 hover:bg-amber-600 hover:text-white text-amber-600 rounded-xl font-bold transition">
                                        <i class='bx bx-edit text-lg'></i>
                                    </button>
                                    <form action="traitement.php" method="POST" onsubmit="return confirm('Supprimer définitivement ce produit du catalogue ? L’historique des ventes restera conservé.');" class="inline">
                                        <input type="hidden" name="action" value="supprimer_produit">
                                        <input type="hidden" name="id_produit" value="<?php echo $p['id_produit']; ?>">
                                        <button <?php echo ($idxCard === 1) ? 'id="btn-supprimer-produit"' : ''; ?> type="submit" title="Supprimer le produit" class="p-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl font-bold transition">
                                            <i class='bx bx-trash text-lg'></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- VUE 2 : TABLEAU CLASSIQUE -->
            <div id="view-table" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-700">Vue Liste détaillée du Catalogue</h3>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-sm border-collapse min-w-[850px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-400 uppercase text-xs border-b border-gray-100">
                                <th class="p-4">Photo</th>
                                <th class="p-4">Marque</th>
                                <th class="p-4">Modèle Exact</th>
                                <th class="p-4">Spécifications</th>
                                <th class="p-4">Processeur / GPU</th>
                                <th class="p-4 text-center">Stock</th>
                                <th class="p-4 text-right">Prix Vente</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($produits as $p): 
                                $nomProduit  = $p['nom_produit'] ?? $p['modele_exact'] ?? 'Produit';
                                $nomMarque   = $p['nom_marque'] ?? 'Marque';
                                $stockageRam = $p['stockage_ram'] ?? 'RAM / SSD non renseignés';
                                $generationPc = (int)($p['generation'] ?? 0);
                                $processeurPc = $p['processeur'] ?? '';
                                $carteGraphiquePc = $p['carte_graphique'] ?? '';
                                $typePc = $p['type_pc'] ?? 'Bureautique';
                                $stock       = (int)($p['quantite_stock'] ?? 0);
                                $prixVente   = (float)($p['prix_vente'] ?? 0);
                                $garantie    = $p['garantie'] ?? '12 Mois';
                                $description = !empty($p['description']) ? $p['description'] : 'Ordinateur portable HP disponible en boutique.';
                                $imgSrcTable = (file_exists(__DIR__ . '/uploads/' . $p['image_url']) && !empty($p['image_url'])) 
                                    ? 'uploads/' . htmlspecialchars($p['image_url']) 
                                    : '';
                                $dataJsonTable = json_encode([
                                    'img' => $imgSrcTable,
                                    'nom' => $nomProduit,
                                    'marque' => $nomMarque,
                                    'ram' => $stockageRam,
                                    'generation' => $generationPc,
                                    'processeur' => $processeurPc,
                                    'carte_graphique' => $carteGraphiquePc,
                                    'type_pc' => $typePc,
                                    'prix' => number_format($prixVente, 0, ',', ' ') . ' FCFA',
                                    'stock' => $stock,
                                    'garantie' => $garantie,
                                    'desc' => $description
                                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                            ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4">
                                        <div class="relative group/tblimg cursor-pointer w-12 h-12 flex-shrink-0"
                                             onclick='ouvrirModalImage(<?php echo $dataJsonTable; ?>)'
                                             title="Cliquer pour agrandir la photo">
                                            <?php if ($imgSrcTable): ?>
                                                <img src="<?php echo $imgSrcTable; ?>" alt="<?php echo htmlspecialchars($nomProduit); ?>" class="w-12 h-12 object-contain rounded-xl border border-gray-200 bg-white p-0.5 group-hover/tblimg:scale-110 group-hover/tblimg:border-indigo-500 transition shadow-sm">
                                            <?php else: ?>
                                                <i class='bx bx-laptop w-12 h-12 flex items-center justify-center text-3xl text-slate-400 border border-gray-200 bg-white rounded-xl'></i>
                                            <?php endif; ?>
                                            <div class="absolute inset-0 bg-indigo-900/40 rounded-xl opacity-0 group-hover/tblimg:opacity-100 transition flex items-center justify-center text-white text-xs">
                                                <i class='bx bx-search-alt text-lg'></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-bold text-gray-800"><?php echo htmlspecialchars($p['nom_marque'] ?? 'Autre'); ?></td>
                                    <td class="p-4 font-extrabold text-indigo-700 text-base"><?php echo htmlspecialchars($nomProduit); ?></td>
                                    <td class="p-4 text-gray-500 font-medium"><?php echo htmlspecialchars($stockageRam); ?> • <?php echo $generationPc ? $generationPc . 'e génération' : htmlspecialchars($typePc); ?></td>
                                    <td class="p-4 text-gray-600 font-semibold"><?php echo htmlspecialchars(trim($processeurPc . ' • ' . $carteGraphiquePc, ' •') ?: $typePc); ?></td>
                                    <td class="p-4 text-center font-bold">
                                        <?php if ($stock <= 3): ?>
                                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-xl font-bold text-xs"><?php echo $stock; ?> (Critique)</span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-xl font-bold text-xs"><?php echo $stock; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-right font-black text-gray-900 text-base"><?php echo number_format($prixVente, 0, ',', ' '); ?> F</td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <button onclick='ouvrirModalEdition(<?php echo json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)' title="Modifier le produit" class="px-2.5 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white rounded-xl font-bold transition text-xs flex items-center">
                                                <i class='bx bx-edit text-base mr-1'></i> Éditer
                                            </button>
                                            <form action="traitement.php" method="POST" onsubmit="return confirm('Supprimer définitivement ce produit du catalogue ? L’historique des ventes restera conservé.');" class="inline">
                                                <input type="hidden" name="action" value="supprimer_produit">
                                                <input type="hidden" name="id_produit" value="<?php echo $p['id_produit']; ?>">
                                                <button type="submit" title="Supprimer" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl font-bold transition text-xs">
                                                    <i class='bx bx-trash text-base'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal de Modification de Produit -->
    <div id="modal-edit-container" class="hidden fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-6 sm:p-8 w-full max-w-3xl space-y-6 relative max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                <h3 class="text-lg font-extrabold text-gray-900 flex items-center">
                    <i class='bx bx-edit text-indigo-600 text-2xl mr-2.5'></i> Modifier la fiche du PC
                </h3>
                <button onclick="fermerModalEdition()" class="text-gray-400 hover:text-gray-600 text-3xl font-light">
                    <i class='bx bx-x'></i>
                </button>
            </div>
            
            <form action="traitement.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <input type="hidden" name="action" value="modifier_produit">
                <input type="hidden" id="edit-id-produit" name="id_produit" value="">
                <input type="hidden" id="edit-ancienne-image" name="ancienne_image" value="">

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Marque :</label>
                    <select id="edit-id-marque" name="id_marque" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 text-sm">
                        <?php foreach ($marques as $m): ?>
                            <option value="<?php echo $m['id_marque']; ?>"><?php echo htmlspecialchars($m['nom_marque']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Modèle Exact :</label>
                    <input type="text" id="edit-nom-produit" name="nom_produit" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm font-bold">
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">RAM / SSD :</label>
                    <input type="text" id="edit-stockage-ram" name="stockage_ram" required placeholder="16 Go RAM / 512 Go SSD" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Type de PC :</label>
                    <select id="edit-type-pc" name="type_pc" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-semibold text-sm">
                        <option value="Bureautique">Bureautique</option>
                        <option value="Gamer">PC Gamer</option>
                        <option value="Professionnel">Professionnel</option>
                            <option value="À reclasser">À reclasser</option>
                    </select>
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Génération :</label>
                    <select id="edit-generation" name="generation" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-semibold text-sm">
                        <option value="">Choisir (8 à 13)</option>
                        <?php for ($generation = 8; $generation <= 13; $generation++): ?>
                            <option value="<?php echo $generation; ?>"><?php echo $generation; ?>e génération</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Processeur :</label>
                    <input type="text" id="edit-processeur" name="processeur" placeholder="Intel Core i5-1135G7" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Carte graphique :</label>
                    <input type="text" id="edit-carte-graphique" name="carte_graphique" placeholder="Intel Iris Xe / NVIDIA RTX" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Prix de Vente (FCFA) :</label>
                    <input type="number" id="edit-prix-vente" name="prix_vente" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-indigo-700 text-sm outline-none">
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Prix d'Achat (FCFA) :</label>
                    <input type="number" id="edit-prix-achat" name="prix_achat" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-sm outline-none">
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Stock Disponible :</label>
                    <input type="number" id="edit-quantite-stock" name="quantite_stock" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none font-bold">
                </div>

                <div>
                    <label class="font-bold text-gray-700 block mb-1">Garantie :</label>
                    <input type="text" id="edit-garantie" name="garantie" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none">
                </div>

                <div class="sm:col-span-2">
                    <label class="font-bold text-gray-700 block mb-1">Changer la Photo (Laisser vide pour conserver) :</label>
                    <input type="file" name="photo" accept="image/*" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 text-xs">
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="font-bold text-gray-700 block mb-1">Description & Particularités :</label>
                    <textarea id="edit-description" name="description" rows="2" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none"></textarea>
                </div>

                <div class="sm:col-span-2 lg:col-span-4 pt-3 flex space-x-3">
                    <button type="button" onclick="fermerModalEdition()" class="w-1/2 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-extrabold text-sm rounded-xl transition">
                        Annuler
                    </button>
                    <button type="submit" class="w-1/2 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center justify-center">
                        <i class='bx bx-check-circle mr-2 text-lg'></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Agrandissement Photo / Lightbox Produit -->
    <div id="modal-lightbox-container" onclick="fermerModalImageOnBackdrop(event)" class="hidden fixed inset-0 z-50 bg-gray-950/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 transition-opacity duration-300">
        <div id="modal-lightbox-card" class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-4xl overflow-hidden flex flex-col md:flex-row relative transform transition-all duration-300 scale-95 opacity-0">
            
            <!-- Bouton Fermer -->
            <button onclick="fermerModalImage()" class="absolute top-4 right-4 z-20 w-10 h-10 bg-gray-900/40 hover:bg-gray-900/80 text-white rounded-full flex items-center justify-center backdrop-blur-xs transition shadow-lg text-2xl font-bold">
                <i class='bx bx-x'></i>
            </button>

            <!-- Section Photo en Grand -->
            <div class="md:w-3/5 bg-gradient-to-br from-gray-900 via-gray-900 to-indigo-950 p-6 sm:p-10 flex items-center justify-center relative min-h-[320px] sm:min-h-[440px]">
                <img id="modal-img-src" src="" alt="Photo du PC" class="hidden max-h-[380px] sm:max-h-[440px] w-auto object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-300">
                <i id="modal-img-placeholder" class="bx bx-laptop text-8xl text-white/60"></i>
                <div class="absolute bottom-4 left-4 right-4 text-center pointer-events-none">
                    <span class="text-xs text-white/70 bg-black/40 backdrop-blur-xs px-3 py-1.5 rounded-full inline-flex items-center">
                        <i class='bx bx-search-alt mr-1.5 text-indigo-400'></i> Aperçu du PC
                    </span>
                </div>
            </div>

            <!-- Section Détails Produit -->
            <div class="md:w-2/5 p-6 sm:p-8 flex flex-col justify-between space-y-6 bg-white">
                <div class="space-y-4">
                    
                    <div class="flex items-center space-x-2 flex-wrap gap-y-2">
                        <span id="modal-img-marque" class="px-3 py-1 bg-indigo-600 text-white text-xs font-black rounded-lg uppercase tracking-wide">
                            MARQUE
                        </span>
                        <span id="modal-img-generation" class="px-2.5 py-0.5 bg-gray-800 text-white text-[10px] font-bold rounded-md">
                            PC portable
                        </span>
                        <span id="modal-img-stock" class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-black rounded-lg">
                            Stock: 0
                        </span>
                    </div>

                    <div>
                        <h3 id="modal-img-nom" class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">
                            Nom du PC
                        </h3>
                        <p id="modal-img-ram" class="text-xs font-semibold text-gray-500 mt-1">
                            256Go / 8Go
                        </p>
                    </div>

                    <div class="pt-2">
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Prix Vente Client</span>
                        <div id="modal-img-prix" class="text-2xl sm:text-3xl font-black text-indigo-700 leading-none">
                            0 FCFA
                        </div>
                    </div>

                    <div class="space-y-2 text-xs border-t border-b border-gray-100 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 font-bold">Processeur :</span>
                            <span id="modal-img-processeur" class="font-extrabold text-gray-800">À préciser</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400 font-bold">Carte graphique :</span>
                            <span id="modal-img-gpu" class="font-extrabold text-gray-800">À préciser</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Fiche technique / Description :</span>
                        <p id="modal-img-desc" class="text-xs text-gray-600 leading-relaxed max-h-32 overflow-y-auto custom-scrollbar">
                            Description du produit...
                        </p>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="fermerModalImage()" class="w-full py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs rounded-xl transition flex items-center justify-center">
                        <i class='bx bx-check mr-1.5 text-base'></i> Fermer l'aperçu
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- SCRIPT JAVASCRIPT -->
    <script>
        function toggleFormAjout() {
            const form = document.getElementById('form-ajout-container');
            form.classList.toggle('hidden');
        }

        // Modal Lightbox d'agrandissement d'image
        function ouvrirModalImage(data) {
            const modalImage = document.getElementById('modal-img-src');
            const modalPlaceholder = document.getElementById('modal-img-placeholder');
            if (data.img) {
                modalImage.src = data.img;
                modalImage.classList.remove('hidden');
                modalPlaceholder.classList.add('hidden');
            } else {
                modalImage.removeAttribute('src');
                modalImage.classList.add('hidden');
                modalPlaceholder.classList.remove('hidden');
            }
            document.getElementById('modal-img-nom').textContent = data.nom || 'PC portable';
            document.getElementById('modal-img-marque').textContent = data.marque || 'Marque';
            document.getElementById('modal-img-ram').textContent = data.ram || '';
            document.getElementById('modal-img-generation').textContent = data.generation ? data.generation + 'e génération' : (data.type_pc || 'PC portable');
            document.getElementById('modal-img-prix').textContent = data.prix || '0 FCFA';
            document.getElementById('modal-img-processeur').textContent = data.processeur || 'À préciser';
            document.getElementById('modal-img-gpu').textContent = data.carte_graphique || data.type_pc || 'À préciser';
            document.getElementById('modal-img-desc').textContent = data.desc || 'Aucune description disponible.';

            const stockBadge = document.getElementById('modal-img-stock');
            const stockQty = parseInt(data.stock) || 0;
            if (stockQty <= 3) {
                stockBadge.className = "px-3 py-1 bg-red-100 text-red-700 text-xs font-black rounded-lg inline-flex items-center";
                stockBadge.innerHTML = "<i class='bx bx-error-circle mr-1'></i> Stock critique : " + stockQty;
            } else {
                stockBadge.className = "px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-black rounded-lg inline-flex items-center";
                stockBadge.innerHTML = "<i class='bx bx-check-double mr-1'></i> Stock : " + stockQty + " disp.";
            }

            const container = document.getElementById('modal-lightbox-container');
            const card = document.getElementById('modal-lightbox-card');
            
            container.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 20);
        }

        function fermerModalImage() {
            const container = document.getElementById('modal-lightbox-container');
            const card = document.getElementById('modal-lightbox-card');
            if (!card) return;
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                container.classList.add('hidden');
            }, 200);
        }

        function fermerModalImageOnBackdrop(event) {
            if (event.target.id === 'modal-lightbox-container') {
                fermerModalImage();
            }
        }

        function ouvrirModalEdition(p) {
            document.getElementById('edit-id-produit').value = p.id_produit || '';
            document.getElementById('edit-id-marque').value = p.id_marque || '';
            document.getElementById('edit-nom-produit').value = p.nom_produit || p.modele_exact || '';
            document.getElementById('edit-stockage-ram').value = p.stockage_ram || '';
            document.getElementById('edit-generation').value = p.generation || '';
            document.getElementById('edit-processeur').value = p.processeur || '';
            document.getElementById('edit-carte-graphique').value = p.carte_graphique || '';
            document.getElementById('edit-type-pc').value = p.type_pc || 'Bureautique';
            document.getElementById('edit-prix-vente').value = p.prix_vente || '';
            document.getElementById('edit-prix-achat').value = p.prix_achat || '';
            document.getElementById('edit-quantite-stock').value = p.quantite_stock || '';
            document.getElementById('edit-garantie').value = p.garantie || '';
            document.getElementById('edit-description').value = p.description || '';
            document.getElementById('edit-ancienne-image').value = p.image_url || '';

            document.getElementById('modal-edit-container').classList.remove('hidden');
        }

        function fermerModalEdition() {
            document.getElementById('modal-edit-container').classList.add('hidden');
        }

        function switchView(mode) {
            const gridView = document.getElementById('view-grid');
            const tableView = document.getElementById('view-table');
            const btnGrid = document.getElementById('btn-grid');
            const btnTable = document.getElementById('btn-table');

            if (mode === 'grid') {
                gridView.classList.remove('hidden');
                tableView.classList.add('hidden');
                btnGrid.className = "px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold transition flex items-center bg-white text-indigo-600 shadow-sm";
                btnTable.className = "px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold transition flex items-center text-gray-500 hover:text-gray-700";
            } else {
                gridView.classList.add('hidden');
                tableView.classList.remove('hidden');
                btnTable.className = "px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold transition flex items-center bg-white text-indigo-600 shadow-sm";
                btnGrid.className = "px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs font-bold transition flex items-center text-gray-500 hover:text-gray-700";
            }
        }

        function filtrerMarque(marque) {
            const cards = document.querySelectorAll('.card-produit');
            const buttons = document.querySelectorAll('.btn-filter');

            buttons.forEach(b => {
                b.className = "btn-filter px-4 py-2 rounded-xl text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition flex-shrink-0";
            });

            if (event && event.target) {
                event.target.className = "btn-filter px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-sm transition flex-shrink-0";
            }

            cards.forEach(card => {
                if (marque === 'tous' || card.dataset.marque.includes(marque)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filtrerRecherche() {
            const queryEl = document.getElementById('input-recherche-produit');
            if (!queryEl) return;
            const query = queryEl.value.toLowerCase();
            const cards = document.querySelectorAll('.card-produit');

            cards.forEach(card => {
                if (card.dataset.nom.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Écouteur clavier pour fermer les modales avec Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fermerModalImage();
                fermerModalEdition();
            }
        });
    </script>

</body>
</html>

