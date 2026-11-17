# Guide d'Installation & Déploiement : JY SHOP STORE

Ce document explique comment installer et configurer **JY SHOP STORE**, une boutique de PC portables HP bureautiques et gamer.

---

## 📋 Prérequis Obligatoires

Sur l'ordinateur de destination, il faut installer l'un des serveurs locaux suivants (au choix) :
- **WampServer** (Recommandé sous Windows) : [Télécharger WampServer](https://www.wampserver.com/)
- OU **XAMPP** : [Télécharger XAMPP](https://www.apachefriends.org/)

*Version PHP requise : PHP 7.4, 8.0, 8.1, 8.2 ou supérieure.*
*Base de données : MySQL 5.7+ ou MariaDB 10.4+*

---

## 📂 Étape 1 : Copier les fichiers de l'application

1. Copiez l'intégralité du dossier du projet `MobileHome`.
2. Collez le dossier dans le répertoire racine du serveur web local :
   - Si vous utilisez **WampServer** : Collez dans `C:\wamp64\www\MobileHome`
   - Si vous utilisez **XAMPP** : Collez dans `C:\xampp\htdocs\MobileHome`

---

## 🗄️ Étape 2 : Démarrer le serveur Web & MySQL

1. Lancez **WampServer** (ou **XAMPP Control Panel**).
2. Assurez-vous que les services **Apache** et **MySQL** sont activés (icône verte dans la barre des tâches pour Wamp, ou statut "Running" dans XAMPP).

---

## 🛢️ Étape 3 : Création & Importation de la Base de Données

### Méthode A : Via phpMyAdmin (Interface Graphique - Recommandé)
1. Ouvrez votre navigateur internet et allez sur : [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Connectez-vous (Identifiant : `root`, Mot de passe : *(laisser vide)*).
3. Cliquez sur l'onglet **Nouvelle base de données** à gauche.
4. Entrez le nom exact de la base de données : `mobilehome_sgc_corrige` (Interclassement : `utf8mb4_unicode_ci`).
5. Cliquez sur **Créer**.
6. Sélectionnez la base `mobilehome_sgc_corrige` nouvellement créée dans la liste à gauche.
7. Cliquez sur l'onglet **Importer** en haut.
8. Cliquez sur **Parcourir...** et sélectionnez le fichier : `MobileHome/mobilehome_sgc_corrige.sql`
9. Cliquez sur le bouton **Importer** tout en bas de la page.

---

## ⚙️ Étape 4 : Vérification du Fichier de Connexion (`connexion.php`)

Ouvrez le fichier `MobileHome/config/connexion.php` et vérifiez les identifiants de connexion MySQL :

```php
$hote        = 'localhost';
$port        = '3306';
$nomBdd      = 'mobilehome_sgc_corrige';
$utilisateur = 'root';
$motDePasse  = ''; // Laissez vide sous Wamp/XAMPP Windows par défaut
```
*(Remarque : Notre code contient un système d'auto-migration PDO qui crée et ajuste automatiquement les colonnes manquantes si besoin).*

---

## 🌐 Étape 5 : Accéder à l'application

Ouvrez votre navigateur web (Chrome, Edge, Firefox) et tapez l'adresse suivante :

👉 **[http://localhost/MobileHome/](http://localhost/MobileHome/)**  
*(ou `http://localhost/MobileHome/MobileHome/login.php` selon la structure de votre dossier)*

---

## 🔑 Étape 6 : Comptes Utilisateurs par Défaut

L'application est pré-configurée avec 3 rôles d'accès :

| Rôle / Post | Identifiant | Mot de Passe |
| :--- | :--- | :--- |
| 🛡️ **Direction Générale (Admin)** | `admin` | `admin2026` |
| 🛒 **Caissier / Vendeur Principal** | `vendeur` | `vendeur2026` |
| 📦 **Chef Stockiste** | `stockiste` | `stock2026` |

---

## 🖨️ Remarque pour l'impression des Factures & Tickets SAV

L'application intègre l'impression directe des factures et des tickets SAV d'atelier.
- Lors du clic sur **"Imprimer Reçu"** ou **"Imprimer Ticket SAV"**, la fenêtre d'impression standard du navigateur s'ouvre.
- Pour une imprimante thermique / caisse (format 80mm), sélectionnez votre imprimante dans la liste des périphériques Windows.

---

## ❓ En cas de problème / Dépannage

- **Erreur "Erreur de connexion à la base de données"** : Vérifiez que MySQL est bien démarré dans Wamp/XAMPP et que la base `mobilehome_sgc_corrige` a bien été créée dans phpMyAdmin.
- **Page blanche ou 404** : Vérifiez le nom du dossier dans `C:\wamp64\www\`.
