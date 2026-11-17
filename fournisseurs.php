<?php
/**
 * Répertoire des Fournisseurs (CRUD)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

$stmtF = $pdo->query("SELECT * FROM table_fournisseurs ORDER BY id_fournisseur DESC");
$fournisseurs = $stmtF->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fournisseurs | JY SHOP STORE</title>
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
                <i class='bx bxs-truck text-2xl sm:text-3xl text-indigo-600 hidden sm:inline-block'></i>
                <div>
                    <h1 class="text-base sm:text-xl font-extrabold text-gray-900 leading-tight">Répertoire des Fournisseurs & Grossistes Partenaires</h1>
                    <p class="text-xs text-gray-500 hidden sm:block">Gestion des contacts d'approvisionnement et catégories fournies.</p>
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

            <!-- Formulaire Ajouter Fournisseur -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h3 class="text-base font-extrabold text-gray-900 flex items-center">
                    <i class='bx bxs-truck text-indigo-600 text-xl mr-2.5'></i> Enregistrer un Nouveau Fournisseur
                </h3>
                
                <form action="traitement.php" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <input type="hidden" name="action" value="ajouter_fournisseur">

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Nom Société / Grossiste :</label>
                        <input type="text" name="nom_societe" required placeholder="Dubai Phone Wholesale LLC" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm font-extrabold">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Contact Référent :</label>
                        <input type="text" name="nom_contact" required placeholder="Mr. Tariq Al-Mansoor" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Téléphone :</label>
                        <input type="text" name="telephone" required placeholder="+971 50 123 4567" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm font-semibold">
                    </div>

                    <div>
                        <label class="font-bold text-gray-700 block mb-1">Email professionnel :</label>
                        <input type="email" name="email" placeholder="contact@fournisseur.com" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="font-bold text-gray-700 block mb-1">Produits / Gammes Fournies :</label>
                        <input type="text" name="produits_fournis" placeholder="PC HP, chargeurs, SSD, RAM et accessoires" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl outline-none text-sm">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 pt-2">
                        <button id="btn-enregistrer-fournisseur" type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-md transition flex items-center">
                            <i class='bx bx-check-circle mr-2 text-lg'></i> Enregistrer Fournisseur
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des fournisseurs -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-extrabold text-gray-900">Fournisseurs Partenaires (<?php echo count($fournisseurs); ?>)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-gray-50 text-gray-400 uppercase text-xs border-b border-gray-100">
                                <th class="p-4">Société / Grossiste</th>
                                <th class="p-4">Contact</th>
                                <th class="p-4">Téléphone & Email</th>
                                <th class="p-4">Gamme Fournie</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($fournisseurs as $f): ?>
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 font-black text-gray-900 text-base"><?php echo htmlspecialchars($f['nom_societe']); ?></td>
                                    <td class="p-4 font-extrabold text-indigo-700"><?php echo htmlspecialchars($f['nom_contact']); ?></td>
                                    <td class="p-4 text-gray-600 font-medium"><?php echo htmlspecialchars($f['telephone']); ?> • <?php echo htmlspecialchars($f['email'] ?? 'N/A'); ?></td>
                                    <td class="p-4 font-semibold text-gray-800"><?php echo htmlspecialchars($f['produits_fournis'] ?? 'PC portables & accessoires informatiques'); ?></td>
                                    <td class="p-4 text-center">
                                        <form action="traitement.php" method="POST" onsubmit="return confirm('Supprimer définitivement ce fournisseur de la base de données ?');" class="inline">
                                            <input type="hidden" name="action" value="supprimer_fournisseur">
                                            <input type="hidden" name="id_fournisseur" value="<?php echo $f['id_fournisseur']; ?>">
                                            <button type="submit" title="Supprimer le fournisseur" class="p-2 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded-xl font-bold transition">
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
