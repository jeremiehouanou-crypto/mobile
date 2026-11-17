<?php
/**
 * Script de Réinitialisation & Seeding de la Base de Données
 * Projet : MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)
 */

require_once __DIR__ . '/connexion.php';

try {
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        die("Erreur: Fichier database.sql introuvable.");
    }

    $sql = file_get_contents($sqlFile);

    // Exécuter les instructions SQL
    $pdo->exec($sql);

    echo "<div style='font-family:sans-serif; padding:20px; color:green; background:#eef9f2; border:1px solid #c2ebd0; rounded:8px;'>";
    echo "<h2> Base de données réinitialisée avec succès !</h2>";
    echo "<p>Toutes les anciennes données ont été vidées et de nouvelles données fictives très réalistes ont été insérées (avec photos de produits).</p>";
    echo "<p><a href='../index.php' style='display:inline-block; padding:10px 15px; background:#10b981; color:white; text-decoration:none; border-radius:5px; font-weight:bold;'>Accéder au Tableau de Bord</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='font-family:sans-serif; padding:20px; color:red; background:#fdf2f2; border:1px solid #f8b4b4;'>";
    echo "<h2>Erreur lors de la réinitialisation</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
