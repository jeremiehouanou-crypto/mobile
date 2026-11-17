<?php
/**
 * Ticket d'Atelier & Décharge SAV Imprimable
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
require_once __DIR__ . '/config/connexion.php';
verifierConnexion();

$idReparation = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Informations Entreprise
$entreprise = $pdo->query("SELECT * FROM table_entreprise LIMIT 1")->fetch();
if (!$entreprise) {
    $entreprise = [
        'nom' => 'JY SHOP STORE',
        'slogan' => '« Le meilleur sinon rien »',
        'adresse' => 'Total, dans la zone de la pharmacie NATION, en face de BAMACCO, Lomé, Togo',
        'tel1' => '+229 90 38 90 62',
        'tel2' => '',
        'whatsapp' => '+229 90 38 90 62',
        'email' => '',
        'logo_url' => ''
    ];
}

// Lecture du ticket de réparation
$stmtR = $pdo->prepare("
    SELECT r.*, 
           c.nom as client_nom_famille, 
           c.prenom as client_prenom, 
           c.telephone as client_tel, 
           c.localisation as client_loc,
           c.email as client_email
    FROM table_reparations r
    LEFT JOIN table_clients c ON r.id_client = c.id_client
    WHERE r.id_reparation = :id
");
$stmtR->execute([':id' => $idReparation]);
$reparation = $stmtR->fetch();

if (!$reparation) {
    die("<div style='font-family:sans-serif; padding:40px; text-align:center;'>
            <h2>Ticket SAV Introuvable</h2>
            <p>Le ticket de réparation spécifié n'existe pas ou a été supprimé.</p>
            <a href='reparations.php' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#4f46e5; color:white; border-radius:8px; text-decoration:none;'>Retour au SAV</a>
         </div>");
}

$nomCompletClient = trim(($reparation['client_nom_famille'] ?? '') . ' ' . ($reparation['client_prenom'] ?? ''));
if (empty($nomCompletClient)) {
    $nomCompletClient = 'Client de Passage (Anonyme)';
}

$devis   = (float)$reparation['devis_est'];
$acompte = (float)$reparation['acompte'];
$solde   = max(0, $devis - $acompte);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TICKET SAV N° <?php echo htmlspecialchars($reparation['code_ticket']); ?> - JY SHOP STORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; font-size: 11px; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="p-4 sm:p-8 text-gray-800">

    <!-- Notification Message si présent -->
    <?php if (!empty($msg)): ?>
        <div class="max-w-xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm font-bold flex items-center justify-between shadow-sm no-print">
            <div class="flex items-center">
                <i class='bx bx-check-circle text-2xl mr-3 text-emerald-600'></i>
                <span><?php echo htmlspecialchars($msg); ?></span>
            </div>
            <button onclick="window.print()" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-extrabold hover:bg-emerald-700 shadow transition flex items-center">
                <i class='bx bx-printer mr-1'></i> Imprimer
            </button>
        </div>
    <?php endif; ?>

    <!-- Ticket Reçu Printable Container -->
    <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-gray-100 space-y-6 print-card">

        <!-- Header Officiel -->
        <div class="text-center border-b border-dashed border-gray-200 pb-5 space-y-2">
            <i class='bx bx-laptop text-4xl text-indigo-600'></i>
            <h1 class="text-xl font-extrabold text-indigo-950 tracking-tight uppercase">JY SHOP STORE</h1>
            <p class="text-xs font-extrabold text-indigo-600">PC portables HP • Bureautique & gaming</p>
            <p class="text-[11px] text-gray-600">Appel / WhatsApp : <?php echo htmlspecialchars($entreprise['whatsapp'] ?: $entreprise['tel1']); ?></p>
            
            <div class="pt-2">
                <span class="inline-block px-4 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-800 font-mono text-xs sm:text-sm font-extrabold rounded-xl shadow-xs">
                    TICKET ATELIER SAV N° : <?php echo htmlspecialchars($reparation['code_ticket']); ?>
                </span>
            </div>
        </div>

        <!-- Meta Informations (Date & Statut) -->
        <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3.5 rounded-2xl border border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 font-bold block text-[10px] uppercase">Date de Prise en Charge</span>
                <span class="font-extrabold text-gray-900"><?php echo date('d/m/Y à H:i', strtotime($reparation['date_reparation'])); ?></span>
            </div>
            <div class="text-right">
                <span class="text-gray-400 font-bold block text-[10px] uppercase">Statut Réparation</span>
                <span class="inline-block px-2.5 py-0.5 bg-amber-100 text-amber-900 font-extrabold rounded-lg text-xs">
                    <?php echo htmlspecialchars($reparation['statut']); ?>
                </span>
            </div>
        </div>

        <!-- Informations Client -->
        <div class="space-y-2 border-b border-gray-100 pb-4 text-xs">
            <h3 class="font-extrabold text-gray-900 uppercase tracking-wider text-[11px] flex items-center text-indigo-700">
                <i class='bx bx-user mr-1.5 text-base'></i> Fiche Identité Client
            </h3>
            <div class="grid grid-cols-2 gap-2 text-gray-700 bg-gray-50/70 p-3 rounded-xl">
                <div>
                    <span class="text-gray-400 font-semibold block text-[10px]">Nom & Prénom(s) :</span>
                    <strong class="text-gray-900 font-bold"><?php echo htmlspecialchars($nomCompletClient); ?></strong>
                </div>
                <div>
                    <span class="text-gray-400 font-semibold block text-[10px]">Téléphone Joignable :</span>
                    <strong class="text-gray-900 font-bold"><?php echo htmlspecialchars($reparation['client_tel'] ?? 'Non renseigné'); ?></strong>
                </div>
                <?php if (!empty($reparation['client_loc'])): ?>
                    <div class="col-span-2 pt-1 border-t border-gray-100">
                        <span class="text-gray-400 font-semibold block text-[10px]">Localisation / Adresse :</span>
                        <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($reparation['client_loc']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Détails Appareil & Diagnostic Atelier -->
        <div class="space-y-2 border-b border-gray-100 pb-4 text-xs">
            <h3 class="font-extrabold text-gray-900 uppercase tracking-wider text-[11px] flex items-center text-indigo-700">
                <i class='bx bx-laptop mr-1.5 text-base'></i> Désignation PC & Diagnostic
            </h3>
            <div class="bg-indigo-50/40 border border-indigo-100 p-3.5 rounded-xl space-y-2">
                <div>
                    <span class="text-gray-400 font-bold block text-[10px] uppercase">Modèle de l'Appareil :</span>
                    <span class="text-sm font-extrabold text-indigo-950"><?php echo htmlspecialchars($reparation['appareil']); ?></span>
                </div>
                <div>
                    <span class="text-gray-400 font-bold block text-[10px] uppercase">Description de la Panne / Symptômes :</span>
                    <p class="text-gray-800 font-semibold leading-relaxed bg-white p-2.5 rounded-lg border border-indigo-50 mt-1">
                        <?php echo nl2br(htmlspecialchars($reparation['description_panne'])); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Tableau Décompte Financier -->
        <div class="space-y-2 border-b border-gray-100 pb-4 text-xs">
            <h3 class="font-extrabold text-gray-900 uppercase tracking-wider text-[11px] flex items-center text-indigo-700">
                <i class='bx bx-calculator mr-1.5 text-base'></i> Décompte Financier & Acompte
            </h3>
            
            <div class="bg-gray-50 rounded-xl p-3.5 space-y-2 border border-gray-100">
                <div class="flex justify-between items-center text-gray-700">
                    <span>Devis Estimatif Réparation :</span>
                    <span class="font-extrabold text-gray-900 text-sm"><?php echo number_format($devis, 0, ',', ' '); ?> FCFA</span>
                </div>
                <div class="flex justify-between items-center text-emerald-700 font-semibold pt-1 border-t border-gray-200/60">
                    <span class="flex items-center"><i class='bx bx-check-circle mr-1'></i> Acompte Réglé à la Décharge :</span>
                    <span class="font-extrabold text-emerald-700 text-sm">- <?php echo number_format($acompte, 0, ',', ' '); ?> FCFA</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 text-indigo-950 font-extrabold text-sm">
                    <span class="uppercase">Reste à Régler à la Livraison :</span>
                    <span class="text-base font-black text-indigo-700 bg-indigo-100/60 px-3 py-1 rounded-lg"><?php echo number_format($solde, 0, ',', ' '); ?> FCFA</span>
                </div>
            </div>
        </div>

        <!-- Engagements et Conditions Particulières avec QR Code -->
        <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100 space-x-3">
            <div class="space-y-1.5 text-[10px] text-gray-500 leading-normal flex-1">
                <p class="font-bold text-gray-700 flex items-center"><i class='bx bx-info-circle mr-1 text-indigo-600'></i> Conditions d'Atelier SAV :</p>
                <ul class="list-disc list-inside space-y-0.5">
                    <li>Présentation obligatoire de ce ticket original pour toute restitution d'appareil.</li>
                    <li>Garantie de 30 jours offerte sur la prestation et les pièces remplacées.</li>
                    <li class="font-bold text-amber-800">NB : Un produit encaissé n'est plus repris sauf par possibilité de troc et complément.</li>
                </ul>
            </div>
            <div class="flex-shrink-0 bg-white p-1 rounded-lg border border-gray-200 shadow-xs">
                <?php 
                    $qrTextSAV = "JY SHOP STORE | Ticket SAV: " . $reparation['code_ticket'] . " | PC: " . $reparation['appareil'] . " | Client: " . $nomCompletClient;
                    $qrUrlSAV = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($qrTextSAV);
                ?>
                <img src="<?php echo $qrUrlSAV; ?>" alt="QR Code SAV" class="w-14 h-14 object-contain">
            </div>
        </div>

        <!-- Zones de Signature -->
        <div class="grid grid-cols-2 gap-4 pt-3 text-[11px] text-gray-700 border-t border-dashed border-gray-200">
            <div class="text-center space-y-8">
                <span class="font-extrabold block text-gray-800">Signature du Client :</span>
                <span class="text-[9px] text-gray-400 block italic">(Lu et approuvé)</span>
            </div>
            <div class="text-center space-y-8">
                <span class="font-extrabold block text-gray-800">Pour JY SHOP STORE :</span>
                <span class="text-[9px] text-gray-400 block italic">(Cachet & Signature Technicien)</span>
            </div>
        </div>

        <!-- Actions non-imprimables -->
        <div class="pt-4 flex flex-col sm:flex-row gap-3 no-print">
            <a href="reparations.php" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-extrabold rounded-xl text-xs text-center transition flex items-center justify-center">
                <i class='bx bx-arrow-back mr-1.5 text-base'></i> Retour à la Liste SAV
            </a>
            <button onclick="window.print()" class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs shadow-md transition flex items-center justify-center">
                <i class='bx bx-printer mr-1.5 text-lg'></i> Imprimer le Ticket SAV
            </button>
        </div>

    </div>

</body>
</html>
