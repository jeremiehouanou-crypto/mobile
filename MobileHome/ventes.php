<?php
/**
 * Historique des Ventes & Factures
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

$stmtV = $pdo->query("
    SELECT v.*, COALESCE(CONCAT(c.nom, ' ', c.prenom), 'Client de Passage') as client_nom
    FROM table_ventes v
    LEFT JOIN table_clients c ON v.id_client = c.id_client
    ORDER BY v.date_vente DESC
");
$ventes = $stmtV->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des ventes | JY SHOP STORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style> body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="flex h-screen overflow-hidden text-gray-800 text-sm">

    <!-- Navigation & Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Contenu Principal -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 flex-shrink-0 sticky top-0 z-20">
            <div class="flex items-center space-x-3">
                <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-xl text-2xl focus:outline-none transition">
                    <i class='bx bx-menu'></i>
                </button>
                <i class='bx bx-receipt text-2xl sm:text-3xl text-indigo-600 hidden sm:inline-block'></i>
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-gray-900 leading-tight">Historique des Ventes & Émission de Factures</h1>
                    <p class="text-xs text-gray-500 hidden sm:block">Consultation des transactions enregistrées au comptoir et impression des reçus.</p>
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

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-extrabold text-gray-900">Toutes les Ventes Enregistrées (<?php echo count($ventes); ?>)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-400 uppercase text-xs border-b border-gray-100">
                                <th class="p-4">Facture N°</th>
                                <th class="p-4">Date & Heure</th>
                                <th class="p-4">Client Attribué</th>
                                <th class="p-4">Mode Règlement</th>
                                <th class="p-4 text-right">Montant Total TTC</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($ventes as $v): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 font-black text-indigo-600 text-base"><?php echo htmlspecialchars($v['code_facture']); ?></td>
                                    <td class="p-4 text-gray-500 font-semibold"><?php echo date('d/m/Y H:i', strtotime($v['date_vente'])); ?></td>
                                    <td class="p-4 font-extrabold text-gray-900"><?php echo htmlspecialchars($v['client_nom']); ?></td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-xl text-xs">
                                            <?php echo htmlspecialchars($v['mode_paiement']); ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-black text-gray-900 text-base"><?php echo number_format($v['total_ttc'], 0, ',', ' '); ?> F CFA</td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <a href="facture.php?id=<?php echo $v['id_vente']; ?>" target="_blank" class="px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white font-bold rounded-xl transition text-xs inline-flex items-center">
                                                <i class='bx bx-printer mr-1.5 text-base'></i> Imprimer Reçu
                                            </a>
                                            <form action="traitement.php" method="POST" onsubmit="return confirm('Annuler/Supprimer cette vente de l\'historique et réintégrer les articles au stock ?');" class="inline">
                                                <input type="hidden" name="action" value="supprimer_vente">
                                                <input type="hidden" name="id_vente" value="<?php echo $v['id_vente']; ?>">
                                                <input type="hidden" name="restaurer_stock" value="1">
                                                <button type="submit" title="Annuler cette vente & réintégrer le stock" class="p-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl font-bold transition">
                                                    <i class='bx bx-trash text-lg'></i>
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
</body>
</html>
