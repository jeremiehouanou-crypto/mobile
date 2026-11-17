<?php
/**
 * Tableau de Bord Principal (Protégé)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$userSession = $_SESSION['utilisateur'];

// Calculs direct SQL simples avec fallback de sécurité
try {
    $stmtCA = $pdo->query("SELECT COALESCE(SUM(total_ttc), 0) as ca FROM table_ventes WHERE DATE(date_vente) = CURDATE()");
    $caJour = $stmtCA ? $stmtCA->fetch()['ca'] : 0;
} catch (Exception $e) { $caJour = 0; }

try {
    $stmtStock = $pdo->query("SELECT COALESCE(SUM(COALESCE(quantite_stock, 0)), 0) as total FROM table_produits");
    $totalStock = $stmtStock ? $stmtStock->fetch()['total'] : 0;
} catch (Exception $e) { $totalStock = 0; }

try {
    $stmtClients = $pdo->query("SELECT COUNT(*) as total FROM table_clients");
    $totalClients = $stmtClients ? $stmtClients->fetch()['total'] : 0;
} catch (Exception $e) { $totalClients = 0; }

try {
    $stmtVentes = $pdo->query("SELECT COUNT(*) as total FROM table_ventes WHERE DATE(date_vente) = CURDATE()");
    $totalVentesJour = $stmtVentes ? $stmtVentes->fetch()['total'] : 0;
} catch (Exception $e) { $totalVentesJour = 0; }

// Requête Alertes Stock Faible (< 3)
try {
    $stmtAlertes = $pdo->query("SELECT COALESCE(nom_produit, modele_exact, 'PC portable') as nom_produit, COALESCE(quantite_stock, 0) as quantite_stock, COALESCE(prix_vente, 0) as prix_vente FROM table_produits WHERE COALESCE(quantite_stock, 0) <= 3 LIMIT 5");
    $alertesStock = $stmtAlertes ? $stmtAlertes->fetchAll() : [];
} catch (Exception $e) { $alertesStock = []; }

// Ventes récentes
try {
    $stmtRecentVentes = $pdo->query("
        SELECT v.code_facture, COALESCE(CONCAT(c.nom, ' ', c.prenom), 'Client de Passage') as client, v.mode_paiement, v.total_ttc, v.date_vente
        FROM table_ventes v
        LEFT JOIN table_clients c ON v.id_client = c.id_client
        ORDER BY v.date_vente DESC LIMIT 5
    ");
    $recentVentes = $stmtRecentVentes ? $stmtRecentVentes->fetchAll() : [];
} catch (Exception $e) { $recentVentes = []; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord | JY SHOP STORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style> 
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; } 
    </style>
</head>
<body class="flex h-screen overflow-hidden text-gray-800 text-sm">

    <!-- Navigation Latérale -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Contenu Principal -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto w-full min-w-0">
        <!-- Topbar -->
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 flex-shrink-0 sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <!-- Bouton Menu Mobile Hamburger -->
                <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-xl text-2xl focus:outline-none transition">
                    <i class='bx bx-menu'></i>
                </button>
                <h2 class="text-xs sm:text-sm font-extrabold text-gray-700 truncate">JY SHOP STORE • GESTION PC</h2>
            </div>

            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <span class="font-extrabold text-gray-900 block text-xs sm:text-sm truncate"><?php echo htmlspecialchars($userSession['nom']); ?></span>
                    <span class="text-[10px] sm:text-xs text-indigo-600 font-bold block"><?php echo htmlspecialchars($userSession['role']); ?></span>
                </div>
                <i class='bx bx-laptop text-3xl text-indigo-600'></i>
            </div>
        </header>

        <div class="p-4 sm:p-8 space-y-6 sm:space-y-8 flex-1">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900">Tableau de Bord Exécutif</h1>
                    <p class="text-xs text-gray-500 mt-1">Bienvenue chez <strong>JY SHOP STORE</strong>, spécialiste des PC HP.</p>
                </div>
            </div>

            <!-- Cartes KPIs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div id="kpi-ca-jour" class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white p-5 sm:p-6 rounded-2xl shadow-md space-y-2">
                    <div class="flex justify-between items-center opacity-85">
                        <span class="text-xs font-extrabold uppercase tracking-wider">CA du Jour</span>
                        <i class='bx bx-trending-up text-2xl'></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black"><?php echo number_format($caJour, 0, ',', ' '); ?> F CFA</h3>
                </div>

                <div id="kpi-stock-total" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-5 sm:p-6 rounded-2xl shadow-md space-y-2">
                    <div class="flex justify-between items-center opacity-85">
                        <span class="text-xs font-extrabold uppercase tracking-wider">Stock Appareils BDD</span>
                        <i class='bx bx-devices text-2xl'></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black"><?php echo $totalStock; ?> Unités</h3>
                </div>

                <div id="kpi-total-clients" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-5 sm:p-6 rounded-2xl shadow-md space-y-2">
                    <div class="flex justify-between items-center opacity-85">
                        <span class="text-xs font-extrabold uppercase tracking-wider">Total Clients</span>
                        <i class='bx bx-group text-2xl'></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black"><?php echo $totalClients; ?> Fiches</h3>
                </div>

                <div class="bg-slate-900 text-white p-5 sm:p-6 rounded-2xl shadow-md space-y-2">
                    <div class="flex justify-between items-center opacity-85 text-slate-400">
                        <span class="text-xs font-extrabold uppercase tracking-wider">Ventes du jour</span>
                        <i class='bx bx-receipt text-2xl text-emerald-400'></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-emerald-400"><?php echo $totalVentesJour; ?> Vente(s)</h3>
                </div>
            </div>

            <!-- Tableaux Alertes & Ventes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                <!-- Alertes Stock -->
                <div id="card-alertes-stock" class="bg-white rounded-2xl shadow-sm border border-red-100 p-5 sm:p-6 space-y-4">
                    <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                        <i class='bx bx-error-circle text-red-500 mr-2 text-2xl'></i> Alertes Stock Critique (<= 3)
                    </h3>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-sm border-collapse min-w-[300px]">
                            <thead>
                                <tr class="bg-gray-50 text-gray-400 uppercase text-xs border-b border-gray-100">
                                    <th class="p-3">Produit</th>
                                    <th class="p-3 text-center">Stock</th>
                                    <th class="p-3 text-right">Prix Vente</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($alertesStock as $a): ?>
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-3 font-extrabold text-gray-800"><?php echo htmlspecialchars($a['nom_produit']); ?></td>
                                        <td class="p-3 text-center">
                                            <span class="px-3 py-1 bg-red-100 text-red-700 font-extrabold rounded-lg text-xs">
                                                <?php echo $a['quantite_stock']; ?> restants
                                            </span>
                                        </td>
                                        <td class="p-3 text-right font-black text-indigo-700"><?php echo number_format($a['prix_vente'], 0, ',', ' '); ?> F</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Transactions Récentes -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 space-y-4">
                    <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                        <i class='bx bx-history text-indigo-600 mr-2 text-2xl'></i> Dernières Ventes en Caisse
                    </h3>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-sm border-collapse min-w-[300px]">
                            <thead>
                                <tr class="bg-gray-50 text-gray-400 uppercase text-xs border-b border-gray-100">
                                    <th class="p-3">Facture</th>
                                    <th class="p-3">Client</th>
                                    <th class="p-3 text-right">Montant TTC</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach ($recentVentes as $v): ?>
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-3 font-extrabold text-indigo-600"><?php echo htmlspecialchars($v['code_facture']); ?></td>
                                        <td class="p-3 text-gray-700 font-semibold"><?php echo htmlspecialchars($v['client']); ?></td>
                                        <td class="p-3 text-right font-black text-gray-900"><?php echo number_format($v['total_ttc'], 0, ',', ' '); ?> F</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
