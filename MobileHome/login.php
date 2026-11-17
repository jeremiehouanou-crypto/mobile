<?php
/**
 * Page de Connexion Obligatoire
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 * 
 * L'accès à l'application nécessite une authentification valide.
 */
session_start();

// Si l'utilisateur est déjà connecté, redirection directe vers le tableau de bord
if (isset($_SESSION['utilisateur'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur = isset($_GET['erreur']) ? $_GET['erreur'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | JY SHOP STORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-6 text-gray-800 text-sm">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-gray-100 p-8 space-y-7">
        
        <!-- Logo & Branding -->
        <div class="text-center space-y-3">
            <i class='bx bx-laptop text-5xl text-indigo-600'></i>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">JY SHOP STORE</h1>
            <p class="text-xs text-gray-500 font-medium">Gestion de la boutique de PC portables.</p>
        </div>

        <!-- Message d'erreur -->
        <?php if (!empty($erreur)): ?>
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-bold flex items-center shadow-sm">
                <i class='bx bx-error-circle text-xl mr-2.5 text-red-600'></i>
                <span><?php echo htmlspecialchars($erreur); ?></span>
            </div>
        <?php endif; ?>

        <!-- Formulaire de Connexion -->
        <form action="traitement.php" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="connexion">

            <div class="space-y-1.5">
                <label class="font-extrabold text-gray-700 block text-xs uppercase tracking-wider">Identifiant :</label>
                <div class="relative">
                    <i class='bx bx-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl'></i>
                    <input type="text" name="identifiant" required placeholder="ex: admin ou vendeur"
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition text-sm">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="font-extrabold text-gray-700 block text-xs uppercase tracking-wider">Mot de passe :</label>
                <div class="relative">
                    <i class='bx bx-lock-alt absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl'></i>
                    <input type="password" name="mot_de_passe" required placeholder="••••••••"
                        class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-semibold outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition text-sm">
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-lg transition duration-200 uppercase tracking-wider flex items-center justify-center">
                <i class='bx bx-log-in-circle text-xl mr-2'></i> Se Connecter
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 font-semibold">© 2026 JY SHOP STORE • Tous droits réservés</p>
    </div>

</body>
</html>
