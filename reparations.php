<?php
/**
 * SAV & Atelier de Réparations
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

$stmtR = $pdo->query("
    SELECT r.*, COALESCE(CONCAT(c.nom, ' ', c.prenom), 'Client SAV') as client_nom
    FROM table_reparations r
    LEFT JOIN table_clients c ON r.id_client = c.id_client
    ORDER BY r.date_reparation DESC
");
$reparations = $stmtR->fetchAll();

$clients = $pdo->query("SELECT * FROM table_clients ORDER BY nom ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAV Informatique | JY SHOP STORE</title>
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
                <i class='bx bx-wrench text-2xl sm:text-3xl text-indigo-600 hidden sm:inline-block'></i>
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-gray-900 leading-tight">Service Après-Vente (SAV) & Tickets de Réparation</h1>
                    <p class="text-xs text-gray-500 hidden sm:block">Prise en charge atelier, devis, acomptes et suivi d'avancement.</p>
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

            <!-- Formulaire Prise en Charge -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                    <i class='bx bx-plus-circle text-indigo-600 text-xl mr-2.5'></i> Ouvrir un Ticket de Réparation Atelier
                </h3>
                
                <form action="traitement.php" method="POST" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 text-xs">
                    <input type="hidden" name="action" value="ajouter_reparation">

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Client :</label>
                        <select name="id_client" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-semibold outline-none text-sm">
                            <option value="0">Client Anonyme</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?php echo $c['id_client']; ?>"><?php echo htmlspecialchars($c['nom']); ?> <?php echo htmlspecialchars($c['prenom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Appareil :</label>
                        <input type="text" name="appareil" required placeholder="ex: HP EliteBook 840 G8" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Description Panne :</label>
                        <input type="text" name="description_panne" required placeholder="ex: Vitre cassée + batterie HS" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Devis Estimatif (FCFA) :</label>
                        <input type="number" name="devis_est" required placeholder="45000" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-sm outline-none">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Acompte Versé (FCFA) :</label>
                        <input type="number" name="acompte" placeholder="20000" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-emerald-700 text-sm outline-none">
                    </div>

                    <div class="sm:col-span-3 lg:col-span-4 pt-2">
                        <button id="btn-nouveau-ticket" type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center">
                            <i class='bx bx-check-circle mr-2 text-lg'></i> Créer Ticket SAV
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des tickets -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                        <i class='bx bx-list-check text-indigo-600 text-xl mr-2'></i> Tickets SAV & Suivi Atelier (<?php echo count($reparations); ?>)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse min-w-[850px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-400 uppercase text-xs border-b border-gray-100">
                                <th class="p-4">N° Ticket</th>
                                <th class="p-4">Client</th>
                                <th class="p-4">Appareil</th>
                                <th class="p-4">Panne Signalée</th>
                                <th class="p-4 text-right">Devis / Acompte</th>
                                <th class="p-4 text-center">Statut Atelier</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php 
                            $idxRep = 0;
                            foreach ($reparations as $r): 
                                $idxRep++;
                                $statut = $r['statut'];
                                $badgeColor = 'bg-amber-100 text-amber-800 border-amber-200';
                                if ($statut === 'En Cours') $badgeColor = 'bg-blue-100 text-blue-800 border-blue-200';
                                if ($statut === 'Prêt') $badgeColor = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                if ($statut === 'Livré') $badgeColor = 'bg-purple-100 text-purple-800 border-purple-200';
                            ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 font-black text-indigo-600 text-base">
                                        <?php echo htmlspecialchars($r['code_ticket']); ?>
                                        <span class="block text-[10px] text-gray-400 font-normal"><?php echo date('d/m/Y', strtotime($r['date_reparation'])); ?></span>
                                    </td>
                                    <td class="p-4 font-extrabold text-gray-900"><?php echo htmlspecialchars($r['client_nom']); ?></td>
                                    <td class="p-4 font-bold text-gray-800"><?php echo htmlspecialchars($r['appareil']); ?></td>
                                    <td class="p-4 text-gray-600 font-medium max-w-xs truncate"><?php echo htmlspecialchars($r['description_panne']); ?></td>
                                    <td class="p-4 text-right">
                                        <span class="font-black text-gray-900 block"><?php echo number_format($r['devis_est'], 0, ',', ' '); ?> F</span>
                                        <span class="text-xs text-emerald-600 font-bold block">Ac. <?php echo number_format($r['acompte'], 0, ',', ' '); ?> F</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="traitement.php" method="POST" class="inline-block">
                                            <input type="hidden" name="action" value="modifier_statut_sav">
                                            <input type="hidden" name="id_reparation" value="<?php echo $r['id_reparation']; ?>">
                                            <select <?php echo ($idxRep === 1) ? 'id="select-statut-sav"' : ''; ?> name="statut" onchange="this.form.submit()" class="px-2.5 py-1 rounded-xl text-xs font-extrabold border outline-none cursor-pointer <?php echo $badgeColor; ?>">
                                                <option value="En Atelier" <?php echo $statut === 'En Atelier' ? 'selected' : ''; ?>>En Atelier</option>
                                                <option value="En Cours" <?php echo $statut === 'En Cours' ? 'selected' : ''; ?>>En Cours</option>
                                                <option value="Prêt" <?php echo $statut === 'Prêt' ? 'selected' : ''; ?>>Prêt</option>
                                                <option value="Livré" <?php echo $statut === 'Livré' ? 'selected' : ''; ?>>Livré</option>
                                            </select>
                                        </form>
                                    </td>
                                     <td class="p-4 text-center">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            <a <?php echo ($idxRep === 1) ? 'id="btn-imprimer-ticket"' : ''; ?> href="ticket_sav.php?id=<?php echo $r['id_reparation']; ?>" title="Imprimer le Ticket SAV" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition">
                                                <i class='bx bx-printer text-base mr-1'></i> Imprimer
                                            </a>
                                            <form action="traitement.php" method="POST" onsubmit="return confirm('Supprimer définitivement ce ticket SAV / réparation de l\'historique ?');" class="inline">
                                                <input type="hidden" name="action" value="supprimer_reparation">
                                                <input type="hidden" name="id_reparation" value="<?php echo $r['id_reparation']; ?>">
                                                <button type="submit" title="Supprimer le ticket SAV" class="p-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl font-bold transition">
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
