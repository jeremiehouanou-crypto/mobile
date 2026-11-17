<?php
/**
 * Service de Trocs & Reprises
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Trocs enregistrés
$stmtT = $pdo->query("
    SELECT t.*, COALESCE(CONCAT(c.nom, ' ', c.prenom), 'Client Troc') as client_nom, COALESCE(t.produit_pris_nom, p.nom_produit, p.modele_exact, 'Produit supprimé') as nouveau_produit
    FROM table_trocs t
    LEFT JOIN table_clients c ON t.id_client = c.id_client
    LEFT JOIN table_produits p ON t.id_produit_pris = p.id_produit
    ORDER BY t.date_troc DESC
");
$trocs = $stmtT->fetchAll();

$clients = $pdo->query("SELECT * FROM table_clients ORDER BY nom ASC")->fetchAll();
$produits = $pdo->query("SELECT * FROM table_produits WHERE type_pc <> 'À reclasser' AND COALESCE(quantite_stock, 1) > 0 ORDER BY COALESCE(nom_produit, modele_exact) ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reprises PC | JY SHOP STORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style> body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="flex h-screen overflow-hidden text-gray-800 text-sm">

    <!-- Navigation & Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 flex-shrink-0 sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-xl text-2xl focus:outline-none transition">
                    <i class='bx bx-menu'></i>
                </button>
                <i class='bx bx-transfer-alt text-2xl sm:text-3xl text-indigo-600 hidden sm:inline-block'></i>
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-gray-900 leading-tight">Gestion des Trocs & Reprises d'Occasion</h1>
                    <p class="text-xs text-gray-500 hidden sm:block">Estimation de la valeur de reprise et calcul de la soulte à payer.</p>
                </div>
            </div>
        </header>

        <div class="p-8 space-y-6 flex-1">
            <?php if (!empty($msg)): ?>
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-bold flex items-center shadow-sm">
                    <i class='bx bx-check-circle text-2xl mr-3 text-emerald-600'></i>
                    <span><?php echo htmlspecialchars($msg); ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'enregistrement de Troc -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                    <i class='bx bx-plus-circle text-indigo-600 text-xl mr-2.5'></i> Enregistrer une Transaction de Troc
                </h3>
                
                <form action="traitement.php" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <input type="hidden" name="action" value="ajouter_troc">

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Attribution Client :</label>
                        <select name="id_client" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-semibold outline-none text-sm">
                            <option value="0">Client Anonyme</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?php echo $c['id_client']; ?>"><?php echo htmlspecialchars($c['nom']); ?> <?php echo htmlspecialchars($c['prenom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Appareil Repris (Occasion) :</label>
                        <input type="text" name="appareil_repris" required placeholder="ex: PC portable HP à reprendre" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Valeur Reprise (FCFA) :</label>
                        <input type="number" name="valeur_reprise" required placeholder="280000" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-amber-700 text-sm outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">PC vendu dans l’échange :</label>
                        <select name="id_produit_pris" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-semibold outline-none text-sm">
                            <?php foreach ($produits as $p): ?>
                                <option value="<?php echo $p['id_produit']; ?>"><?php echo htmlspecialchars($p['nom_produit'] ?? $p['modele_exact']); ?> - <?php echo number_format($p['prix_vente'], 0, ',', ' '); ?> F</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 pt-2">
                        <button id="btn-valider-troc" type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center">
                            <i class='bx bx-calculator mr-2 text-lg'></i> Valider & Calculer Soulte
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des trocs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-extrabold text-gray-900">Historique des Trocs Enregistrés (<?php echo count($trocs); ?>)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-400 uppercase text-xs border-b border-gray-100">
                                <th class="p-4">Code Troc</th>
                                <th class="p-4">Client</th>
                                <th class="p-4">Appareil Repris</th>
                                <th class="p-4">Valeur Reprise</th>
                                <th class="p-4">PC concerné</th>
                                <th class="p-4 text-right">Soulte À Payer</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($trocs as $t): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 font-black text-indigo-600 text-base"><?php echo htmlspecialchars($t['code_troc']); ?></td>
                                    <td class="p-4 font-extrabold text-gray-900"><?php echo htmlspecialchars($t['client_nom']); ?></td>
                                    <td class="p-4 font-semibold text-amber-800"><?php echo htmlspecialchars($t['appareil_repris']); ?></td>
                                    <td class="p-4 font-bold text-gray-800"><?php echo number_format($t['valeur_reprise'], 0, ',', ' '); ?> F</td>
                                    <td class="p-4 font-extrabold text-indigo-700"><?php echo htmlspecialchars($t['nouveau_produit'] ?? 'PC portable'); ?></td>
                                    <td class="p-4 text-right font-black text-emerald-600 text-base"><?php echo number_format($t['soulte_payee'], 0, ',', ' '); ?> F CFA</td>
                                    <td class="p-4 text-center">
                                        <form action="traitement.php" method="POST" onsubmit="return confirm('Supprimer définitivement cette transaction de troc de l\'historique ?');" class="inline">
                                            <input type="hidden" name="action" value="supprimer_troc">
                                            <input type="hidden" name="id_troc" value="<?php echo $t['id_troc']; ?>">
                                            <button type="submit" title="Supprimer la transaction de troc" class="p-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl font-bold transition">
                                                <i class='bx bx-trash text-lg'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
