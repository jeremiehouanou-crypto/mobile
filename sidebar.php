<?php
/**
 * Barre de Navigation Latérale Réutilisable (Sidebar) avec Visite Guidée / Tutoriel Onboarding Ultra-Détaillé
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!-- Overlay pour écran mobile (Fermeture au clic à l'extérieur) -->
<div id="sidebar-overlay" onclick="toggleMobileSidebar()" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden transition-opacity"></div>

<!-- Sidebar Réactive -->
<aside id="main-sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col flex-shrink-0 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
    
    <!-- En-tête de la Sidebar avec Bouton Fermer sur Mobile -->
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3 mx-auto lg:mx-0">
            <i class='bx bx-laptop text-3xl text-indigo-600'></i>
            <span class="text-lg font-black tracking-tight text-gray-900">JY SHOP <span class="text-indigo-600">STORE</span></span>
        </div>
        <button onclick="toggleMobileSidebar()" class="lg:hidden text-gray-400 hover:text-gray-700 text-2xl p-1 rounded-lg hover:bg-gray-100 transition">
            <i class='bx bx-x'></i>
        </button>
    </div>

    <!-- Liens du Menu -->
    <div class="flex-1 overflow-y-auto p-4 space-y-1.5 text-sm font-semibold custom-scrollbar">
        <a id="nav-dashboard" href="dashboard.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'dashboard.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bxs-dashboard text-xl mr-3 text-indigo-600'></i> Tableau de Bord
        </a>

        <a id="nav-caisse" href="caisse.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'caisse.php') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-emerald-700 hover:bg-emerald-50'; ?>">
            <i class='bx bx-laptop text-xl mr-3 text-emerald-600'></i> Caisse / POS
        </a>

        <a id="nav-produits" href="produits.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'produits.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bx-devices text-xl mr-3 text-indigo-600'></i> Produits & Stock
        </a>

        <a id="nav-ventes" href="ventes.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'ventes.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bx-cart text-xl mr-3 text-indigo-600'></i> Historique Ventes
        </a>

        <a id="nav-trocs" href="trocs.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'trocs.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bx-transfer-alt text-xl mr-3 text-indigo-600'></i> Reprises PC
        </a>

        <a id="nav-sav" href="reparations.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'reparations.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bx-wrench text-xl mr-3 text-indigo-600'></i> SAV Informatique
        </a>

        <a id="nav-clients" href="clients.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'clients.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bx-group text-xl mr-3 text-indigo-600'></i> Clients
        </a>

        <a id="nav-fournisseurs" href="fournisseurs.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'fournisseurs.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bxs-truck text-xl mr-3 text-indigo-600'></i> Fournisseurs
        </a>

        <a id="nav-utilisateurs" href="utilisateurs.php" class="flex items-center px-4 py-3 rounded-xl transition <?php echo ($currentPage === 'utilisateurs.php') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50'; ?>">
            <i class='bx bx-shield-quarter text-xl mr-3 text-indigo-600'></i> Utilisateurs (RBAC)
        </a>
    </div>

    <!-- Bouton Déconnexion dans la Sidebar -->
    <div class="p-4 border-t border-gray-100 mt-auto">
        <a href="logout.php" class="flex items-center justify-center px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition shadow-sm">
            <i class='bx bx-log-out mr-2 text-lg'></i> Déconnexion
        </a>
    </div>
</aside>
