<?php
/**
 * Script de Génération de Facture Normalisée (Format Imprimable & PDF)
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */

require_once __DIR__ . '/config/database.php';

$codeFacture = isset($_GET['ref']) ? $_GET['ref'] : 'FAC-2026-1024';

$db = Database::getInstance();
$boutique = $db->query("SELECT tel1, whatsapp FROM table_entreprise LIMIT 1")->fetch() ?: [];
$telephoneBoutique = $boutique['whatsapp'] ?? $boutique['tel1'] ?? '+229 90 38 90 62';
$stmtVente = $db->prepare("
    SELECT v.*, CONCAT(c.nom, ' ', c.prenom) as client_nom, c.telephone as client_tel, c.localisation
    FROM table_ventes v
    LEFT JOIN table_clients c ON v.id_client = c.id_client
    WHERE v.code_facture = :ref
");
$stmtVente->execute([':ref' => $codeFacture]);
$vente = $stmtVente->fetch();
$lignesFacture = [];

if ($vente) {
    $stmtLignes = $db->prepare("
        SELECT COALESCE(l.produit_nom, p.nom_produit, p.modele_exact, 'PC supprimé') AS produit_nom,
               COALESCE(l.produit_stockage_ram, p.stockage_ram, '') AS specifications,
               l.quantite, l.prix_unitaire
        FROM table_lignes_vente l
        LEFT JOIN table_produits p ON p.id_produit = l.id_produit
        WHERE l.id_vente = :id_vente
    ");
    $stmtLignes->execute([':id_vente' => $vente['id_vente']]);
    $lignesFacture = $stmtLignes->fetchAll();
} else {
    // Mode Démo si la référence n'est pas encore enregistrée en BDD
    $vente = [
        'code_facture' => $codeFacture,
        'date_vente' => date('Y-m-d H:i:s'),
        'client_nom' => 'Bamba Fatoumata',
        'client_tel' => '+228 90 12 34 56',
        'mode_paiement' => 'T-Money',
        'total_ttc' => 590000
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>REÇU DE VENTE - <?php echo htmlspecialchars($vente['code_facture']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8 font-sans">

    <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-md border border-gray-200" id="receipt">
        
        <!-- En-tête boutique -->
        <div class="text-center border-b border-gray-100 pb-4 mb-4">
            <h1 class="text-lg font-extrabold text-indigo-900 tracking-tight">JY SHOP STORE</h1>
            <p class="text-xs font-bold text-gray-600">PC portables HP • Bureautique & gaming</p>
            <p class="text-[11px] text-gray-500">Appel / WhatsApp : <?php echo htmlspecialchars($telephoneBoutique); ?></p>
            
            <div class="mt-3 inline-block px-3 py-1 bg-indigo-50 text-indigo-700 font-mono text-xs font-bold rounded-lg border border-indigo-100">
                FACTURE N° : <?php echo htmlspecialchars($vente['code_facture']); ?>
            </div>
        </div>

        <!-- Informations Client & Date -->
        <div class="text-xs space-y-1 border-b border-gray-100 pb-3 mb-3 text-gray-700">
            <div class="flex justify-between"><span>Date :</span> <strong class="text-gray-900"><?php echo date('d/m/Y H:i', strtotime($vente['date_vente'])); ?></strong></div>
            <div class="flex justify-between"><span>Client :</span> <strong class="text-gray-900"><?php echo htmlspecialchars($vente['client_nom']); ?></strong></div>
            <div class="flex justify-between"><span>Mode de Règlement :</span> <strong class="text-indigo-600"><?php echo htmlspecialchars($vente['mode_paiement']); ?></strong></div>
        </div>

        <!-- Articles Vendus -->
        <div class="border-b border-gray-100 pb-3 mb-3 space-y-2 text-xs">
            <div class="flex justify-between font-bold text-gray-800">
                <span>Article / PC</span>
                <span>Prix (FCFA)</span>
            </div>
            <?php foreach ($lignesFacture as $ligne): ?>
                <div class="flex justify-between gap-3 text-gray-700 border-t border-gray-50 pt-1">
                    <span><?php echo (int)$ligne['quantite']; ?>x <?php echo htmlspecialchars($ligne['produit_nom']); ?> <?php echo htmlspecialchars($ligne['specifications']); ?></span>
                    <span class="font-bold text-indigo-900"><?php echo number_format((float)$ligne['quantite'] * (float)$ligne['prix_unitaire'], 0, ',', ' '); ?> F</span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Total TTC -->
        <div class="flex justify-between items-center font-bold text-sm text-indigo-900 bg-indigo-50 p-3 rounded-xl mb-4 border border-indigo-100 shadow-xs">
            <span>TOTAL RÉGLÉ TTC</span>
            <span class="text-base font-black"><?php echo number_format($vente['total_ttc'], 0, ',', ' '); ?> FCFA</span>
        </div>

        <!-- Bloc QR Code & Sécurité Facture -->
        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between mb-4">
            <div class="space-y-1 min-w-0 pr-3">
                <span class="text-[10px] font-black uppercase text-indigo-900 block tracking-wider flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1 text-indigo-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Authenticité Facture
                </span>
                <p class="text-[10px] text-gray-500 leading-tight">Scannez ce QR Code pour contrôler la validité du reçu au comptoir.</p>
                <span class="text-[10px] font-mono font-extrabold text-gray-700 block truncate"><?php echo htmlspecialchars($vente['code_facture']); ?></span>
            </div>
            <div class="flex-shrink-0 bg-white p-1 rounded-lg border border-gray-200 shadow-xs">
                <?php 
                    $qrText = "JY SHOP STORE | Facture: " . $vente['code_facture'] . " | Total: " . number_format($vente['total_ttc'], 0, ',', ' ') . " FCFA | Date: " . date('d/m/Y H:i', strtotime($vente['date_vente'])) . " | Client: " . $vente['client_nom'];
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($qrText);
                ?>
                <img src="<?php echo $qrUrl; ?>" alt="QR Code Facture" class="w-16 h-16 object-contain">
            </div>
        </div>

        <!-- Pied de Facture & Garantie -->
        <div class="text-center text-[10px] text-gray-500 space-y-1.5 border-t border-gray-100 pt-3">
            <div class="p-2 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg font-bold uppercase text-[9.5px] leading-snug">
                ⚠️ NB : Un produit encaissé n'est plus repris sauf par possibilité de troc et complément.
            </div>
            <p>Garantie Officielle 12 Mois avec assistance technique SAV.</p>
            <p class="font-extrabold text-indigo-900">Merci pour votre achat chez JY SHOP STORE !</p>
        </div>

        <!-- Action Imprimer -->
        <div class="mt-6 flex space-x-3 no-print">
            <button onclick="window.close()" class="w-1/2 py-2 bg-gray-100 text-gray-600 font-semibold rounded-lg text-xs">Fermer</button>
            <button onclick="window.print()" class="w-1/2 py-2 bg-indigo-600 text-white font-semibold rounded-lg text-xs shadow-md hover:bg-indigo-700">Imprimer / PDF</button>
        </div>

    </div>

</body>
</html>
