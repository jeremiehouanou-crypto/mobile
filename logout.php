<?php
/**
 * Script de Déconnexion
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 * 
 * Détruit la session active et redirige l'utilisateur vers login.php
 */
session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;
