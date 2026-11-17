# 📘 Guide Utilisateur Complet & Description Détaillée des Interfaces
## Application de Gestion MOBILE HOME TOGO (SGC-SMARTPHONE V1.0)

Ce document est le **manuel d'utilisation officiel et exhaustif** de votre logiciel de gestion **Mobile Home Togo**.  
Il décrit **chaque écran (interface)**, **chaque champ**, **chaque fonctionnalité** et **l'action exacte de chaque bouton** du système, dans un langage simple et accessible à tous.

---

## 📑 Sommaire des Interfaces

1. [Menu Latéral & Navigation (Sidebar)](#1-📱-menu-latéral--navigation-sidebar)
2. [Écran de Connexion (login.php)](#2-🔑-écran-de-connexion-loginphp)
3. [Tableau de Bord / Accueil (index.php)](#3-📊-tableau-de-bord--accueil-indexphp)
4. [Catalogue des Smartphones & Stocks (produits.php)](#4-📱-catalogue-des-smartphones--stocks-produitsphp)
5. [Interface d'Édition / Modification de Produit (Modal)](#5-✏️-interface-dédition--modification-de-produit-modal)
6. [Caisse & Point de Vente POS (caisse.php)](#6-🛒-caisse--point-de-vente-pos-caissephp)
7. [Facture Normalisée Imprimable (facture.php)](#7-🖨️-facture-normalisée-imprimable-facturephp)
8. [Trocs & Reprises d'Appareils (trocs.php)](#8-🔄-trocs--reprises-dappareils-trocsphp)
9. [Service Après-Vente & Atelier SAV (reparations.php)](#9-🛠️-service-après-vente--atelier-sav-reparationsphp)
10. [Fiche d'Impression du Ticket SAV (ticket_sav.php)](#10-📋-fiche-dimpression-du-ticket-sav-ticket_savphp)
11. [Répertoire des Clients (clients.php)](#11-👥-répertoire-des-clients-clientsphp)
12. [Gestion des Fournisseurs (fournisseurs.php)](#12-🏢-gestion-des-fournisseurs-fournisseursphp)
13. [Gestion des Utilisateurs & Rôles (utilisateurs.php)](#13-🔐-gestion-des-utilisateurs--rôles-utilisateursphp)

---

## 1. 📱 Menu Latéral & Navigation (Sidebar)

Le menu latéral est présent à gauche de toutes les pages de l'application. Il permet de naviguer instantanément d'une section à une autre.

### 🔘 Boutons & Liens du Menu Latéral :
- **Logo Mobile Home Togo :** Identifie l'enseigne officielle.
- **Tableau de Bord :** Redirige vers la page d'accueil avec toutes les statistiques globales.
- **Caisse / POS :** Ouvre l'interface de vente rapide au comptoir.
- **Produits & Stock :** Ouvre le catalogue général des smartphones, la gestion des stocks et des photos.
- **Historique Ventes :** Affiche la liste de toutes les ventes réalisées avec filtres et reçus.
- **Trocs & Reprises :** Ouvre le module d'échange de téléphones usagés contre des téléphones neufs.
- **SAV Réparations :** Accède à l'atelier de réparation, au suivi des pannes et aux tickets SAV.
- **Clients :** Ouvre le répertoire des clients et le suivi des achats cumulés (fidélité).
- **Fournisseurs :** Ouvre la liste des fournisseurs et grossistes de la boutique.
- **Utilisateurs (RBAC) :** Accessible aux administrateurs pour créer et gérer les comptes d'accès.
- 🧭 **Bouton "Visite Guidée (Tutoriel)" :** Bouton jaune avec boussole animée. Cliquez dessus à tout moment pour lancer le tutoriel interactif guidé qui entoure les éléments à l'écran !
- 🚪 **Bouton "Déconnexion" :** Bouton rouge tout en bas. Ferme votre session en toute sécurité et vous ramène à la page de connexion.

---

## 2. 🔑 Écran de Connexion (`login.php`)

### 📌 Rôle de cette interface :
C'est la porte d'entrée sécurisée du logiciel. Elle protège les données de votre boutique et garantit que seul le personnel autorisé peut accéder aux ventes et stocks.

### 🔍 Description des Champs & Boutons :
- **Logo & Titre :** Affiche l'identité visuelle de Mobile Home Togo.
- **Champ "Identifiant" :** Saisissez votre nom d'utilisateur (ex: `admin`, `vendeur`, `stockiste`).
- **Champ "Mot de passe" :** Saisissez votre mot de passe de connexion.
- **Bouton "Se connecter" (Bleu) :** Vérifie vos identifiants et ouvre votre session.
- **Boîte d'Alerte Erreur (Rouge) :** Si l'identifiant ou le mot de passe est incorrect, ce message apparaît pour vous prévenir.

---

## 3. 📊 Tableau de Bord / Accueil (`index.php`)

### 📌 Rôle de cette interface :
C'est le centre de contrôle principal. En un coup d'œil, il vous donne une vision claire de la santé financière et commerciale de votre boutique.

### 🔍 Description des Cartes d'Indicateurs (KPIs) :
1. 💰 **Carte "Chiffre d'Affaires Total" :** Affiche la somme totale de toutes les ventes réalisées en FCFA.
2. 📦 **Carte "Smartphones en Stock" :** Indique le nombre total d'unités de téléphones physiques en réserve dans la boutique.
3. ⚠️ **Carte "Alertes Stock Critique" :** Compte le nombre de modèles dont le stock est égal ou inférieur à 3 unités (affiché en rouge).
4. 🛠️ **Carte "Réparations Atelier (SAV)" :** Affiche le nombre de tickets de réparation actuellement pris en charge par l'atelier.

### 🔍 Description des Tableaux & Actions :
- **Tableau "Dernières Ventes Réalisées" :** Affiche la liste des transactions récentes avec le code facture, le nom du client, le total payé et le mode de règlement.
- **Tableau "Smartphones en Alerte de Stock" :** Liste les téléphones dont la réserve est presque vide avec leur quantité restante.
- **Bouton "Ajouter du Stock / Éditer" :** Permet de basculer immédiatement sur la fiche du produit pour augmenter la quantité.

---

## 4. 📱 Catalogue des Smartphones & Stocks (`produits.php`)

### 📌 Rôle de cette interface :
C'est le catalogue général de la boutique. Il permet d'ajouter de nouveaux téléphones, d'uploader des photos, de régler les prix et de gérer les stocks.

### 🔍 Description des Boutons de l'En-tête :
- 🔲 **Bouton "Grille" :** Affiche les téléphones sous forme de cartes visuelles modernes avec leurs photos et leurs pastilles de couleurs.
- 📋 **Bouton "Tableau" :** Bascule vers une vue en liste détaillée compacte.
- ➕ **Bouton "+ Nouveau Smartphone" (Bleu) :** Ouvre ou ferme le formulaire d'enregistrement de produit.

### 🔍 Formulaire d'Ajout de Smartphone :
- **Sélecteur "Marque" :** Choisissez la marque (Apple, Samsung, Xiaomi, Google Pixel, Tecno, Infinix...).
- **Champ "Modèle Exact" :** Entrez le nom complet (ex: *iPhone 16 Pro Max*).
- **Champ "Stockage & RAM" :** Entrez la mémoire (ex: *256Go / 8Go*).
- **Sélecteur "Réseau" :** Choisissez *5G* ou *4G*.
- **Champ "Prix de Vente Client (FCFA)" :** Entrez le prix de vente final.
- **Champ "Prix d'Achat Fournisseur (FCFA)" :** Entrez le prix d'achat grossiste (pour calculer vos marges).
- **Champ "Quantité Stock Initial" :** Nombre d'unités ajoutées au stock.
- **Champ "Garantie Constructeur" :** Exemple : *12 Mois Apple*.
- **Champ "Couleurs Disponibles" :** Exemple : *Titane Désert, Noir, Argent*.
- **Champ "Photo Officielle du Produit" :** Cliquez sur "Choisir un fichier" pour importer une image depuis l'ordinateur.
- **Champ "Description & Particularités" :** Spécifications de l'appareil (ex: *Capteur 200MP Leica, Écran AMOLED 120Hz*).
- 🔘 **Bouton "Enregistrer & Téléverser au Catalogue" :** Enregistre le produit en base de données et téléverse la photo.

### 🔍 Barre de Recherche & Filtres :
- **Filtres Marque (Boutons gris/bleus) :** Cliquez sur "Tous", "Apple", "Samsung", etc. pour filtrer l'affichage par marque en 1 clic.
- **Barre de Recherche :** Tapez n'importe quel mot (ex: *Pixel*, *256Go*, *Pro*) pour filtrer la liste en temps réel.

### 🔍 Actions sur chaque Carte Produit (Mode Grille et Tableau) :
- 🏷️ **Badges de Stock :** Vert si le stock est suffisant (`Stock: 12`), Rouge si le stock est critique (`Stock: 2`).
- 🛒 **Bouton "Vendre" (Icône Caddie) :** Redirige immédiatement vers la caisse avec ce produit pré-sélectionné.
- ✏️ **Bouton "Éditer" (Icône Crayon Jaune) :** Ouvre la fenêtre modale de modification des informations du téléphone.
- 🗑️ **Bouton "Supprimer" (Icône Poubelle Rouge) :** Demande confirmation et supprime le produit du catalogue.

---

## 5. ✏️ Interface d'Édition / Modification de Produit (Modal)

### 📌 Rôle de cette interface :
Lorsque vous cliquez sur le bouton jaune **Éditer** d'un produit, cette fenêtre modale s'ouvre au milieu de l'écran sans quitter la page.

### 🔍 Éléments & Boutons :
- **Données pré-remplies :** Tous les champs (Marque, Modèle, RAM, Prix, Stock, Couleurs, Description) sont automatiquement chargés avec les valeurs actuelles du téléphone.
- **Champ Photo (Optionnel) :** Si vous sélectionnez une nouvelle photo, elle remplace l'ancienne. Si vous laissez le champ vide, la photo actuelle est conservée.
- ⚪ **Bouton "Annuler" (Gris) :** Ferme la fenêtre sans enregistrer les modifications.
- 🔵 **Bouton "Enregistrer les Modifications" (Bleu) :** Met à jour la base de données et actualise l'affichage.

---

## 6. 🛒 Caisse & Point de Vente POS (`caisse.php`)

### 📌 Rôle de cette interface :
C'est l'outil de vente au comptoir. Il permet aux vendeurs et caissiers d'enregistrer les achats des clients en quelques secondes.

### 🔍 Description des Champs & Boutons :
- **Sélecteur "Smartphone à vendre" :** Choisissez le téléphone dans la liste déroulante (affiche le nom, le stock disponible et le prix).
- **Champ "Quantité vendue" :** Indiquez le nombre d'appareils achetés (ex: 1, 2).
- **Sélecteur "Client" :** Sélectionnez un client enregistré dans votre répertoire ou laissez *Client de Passage*.
- **Sélecteur "Mode de Règlement" :** Choisissez le moyen de paiement utilisé :
  - *Espèces* (Comptant)
  - *Flooz* (Mobile Money Moov)
  - *T-Money* (Mobile Money Togocom)
  - *Carte Bancaire*
- 🟢 **Bouton "Valider la Vente & Éditer la Facture" (Vert) :**
  1. Décrémente automatiquement le stock du produit.
  2. Calcule le montant total TTC.
  3. Ajoute l'achat au cumul du client s'il est enregistré.
  4. Redirige immédiatement vers la **Facture Imprimable**.

---

## 7. 🖨️ Facture Normalisée Imprimable (`facture.php`)

### 📌 Rôle de cette interface :
Affiche la facture au format reçu/ticket officiel, prête à être imprimée et remise au client.

### 🔍 Éléments de la Facture :
- **En-tête Officiel :** Logo Mobile Home Togo, slogan, adresse complète et numéros Tél/WhatsApp.
- **Badge N° Facture :** Code unique normalisé (ex: `FAC-2026-8492`).
- **Informations Vente :** Date et heure exactes, Nom du client, Mode de règlement.
- **Tableau des Articles :** Nom du smartphone, configuration RAM/Stockage, Quantité et Prix total en FCFA.
- **Encadré "TOTAL RÉGLÉ TTC" :** Somme totale payée par le client.
- **Mentions Légales :** Rappel de la Garantie Officielle 12 Mois et remerciements.

### 🔍 Boutons d'Action (non imprimables) :
- ⚪ **Bouton "Retour Caisse" (Gris) :** Revient à l'écran de caisse pour effectuer une nouvelle vente.
- 🟣 **Bouton "Imprimer Reçu" (Violet) :** Ouvre la fenêtre d'impression native du navigateur pour imprimer sur votre imprimante de caisse ou papier A4.

---

## 8. 🔄 Trocs & Reprises d'Appareils (`trocs.php`)

### 📌 Rôle de cette interface :
Gère l'échange de téléphones d'occasion ramenés par les clients contre des appareils de votre boutique.

### 🔍 Description des Champs & Boutons :
- **Sélecteur "Client" :** Le client effectuant le troc.
- **Champ "Appareil Repris par la boutique" :** Modèle et description du téléphone du client (ex: *iPhone 12 128Go usagé*).
- **Champ "Valeur de Reprise accordée (FCFA)" :** Le montant d'estimation accordé (ex: *180 000 FCFA*).
- **Sélecteur "Smartphone Pris dans la boutique" :** Sélection de l'appareil neuf/reconditionné choisi par le client dans votre stock.
- **Champ "Prix Vente de l'appareil pris (FCFA)" :** Le prix du nouvel appareil (ex: *680 000 FCFA*).
- 🔵 **Bouton "Valider le Troc" (Bleu) :**
  - Calcule automatiquement la **Soulte** (Exemple : 680 000 - 180 000 = **500 000 FCFA à rajouter par le client**).
  - Enregistre la transaction dans l'historique des trocs.

---

## 9. 🛠️ Service Après-Vente & Atelier SAV (`reparations.php`)

### 📌 Rôle de cette interface :
Permet de réceptionner les téléphones en panne, d'ouvrir des fiches de réparation, de suivre l'avancement et d'imprimer les reçus d'atelier.

### 🔍 Formulaire d'Ouverture de Ticket SAV :
- **Sélecteur "Client" :** Propriétaire de l'appareil.
- **Champ "Appareil" :** Marque et modèle (ex: *Samsung Galaxy Note 20*).
- **Champ "Description Panne" :** Explication détaillée du dysfonctionnement (ex: *Vitre cassée + Batterie gonflée*).
- **Champ "Devis Estimatif (FCFA)" :** Le prix total prévu pour la réparation.
- **Champ "Acompte Versé (FCFA)" :** L'avance financière payée par le client au dépôt.
- 🔵 **Bouton "Créer Ticket SAV" :** Valide la prise en charge et ouvre automatiquement le ticket d'impression.

### 🔍 Tableau des Tickets & Actions :
- **N° Ticket & Date :** Identifiant unique du ticket (ex: `SAV-2026-391`).
- **Devis / Acompte :** Affiche le devis total et l'acompte versé.
- 🔄 **Sélecteur interactif "Statut Atelier" :** Permet de changer l'état de la réparation en 1 clic :
  - `En Atelier` (Jaune) : Appareil réceptionné.
  - `En Cours` (Bleu) : Réparation en cours par le technicien.
  - `Prêt` (Vert) : Réparation terminée, appareil disponible.
  - `Livré` (Violet) : Appareil remis au client et solde réglé.
- 🖨️ **Bouton "Imprimer" (Bleu avec icône d'imprimante) :** Ouvre la fiche d'impression du ticket SAV pour cet appareil.

---

## 10. 📋 Fiche d'Impression du Ticket SAV (`ticket_sav.php`)

### 📌 Rôle de cette interface :
Génère le reçu d'atelier officiel / décharge de réparation prêt à être imprimé pour le client.

### 🔍 Éléments du Ticket SAV :
- **En-tête de la Boutique :** Logo, Nom, Slogan, Adresse, Contacts WhatsApp et Téléphones.
- **Badge N° Ticket SAV :** Exemple `SAV-2026-391`.
- **Informations Client :** Nom complet, Téléphone joignable et Localisation.
- **Désignation & Diagnostic :** Modèle de l'appareil et description complète de la panne.
- **Décompte Financier :**
  - Devis Estimatif Réparation (FCFA)
  - Acompte Réglé à la Décharge (FCFA)
  - **Reste à Régler à la Livraison (FCFA)** (Calculé automatiquement).
- **Conditions d'Atelier SAV :** Mentions sur la garantie 30 jours, la présentation du ticket obligatoire et les délais de garde.
- **Zones de Signature :** Espace pour la signature du client et le cachet/signature du technicien.

### 🔍 Boutons d'Action (non imprimables) :
- ⚪ **Bouton "Retour à la Liste SAV" (Gris) :** Redirige vers l'atelier de réparation.
- 🔵 **Bouton "Imprimer le Ticket SAV" (Bleu) :** Lance l'impression native du ticket.

---

## 11. 👥 Répertoire des Clients (`clients.php`)

### 📌 Rôle de cette interface :
Conserve la liste de tous vos clients, leurs coordonnées et leur niveau de fidélité.

### 🔍 Description des Champs & Boutons :
- **Formulaire d'Ajout :** Nom, Prénom, Téléphone, Email, Localisation (Quartier/Ville).
- 🔵 **Bouton "Enregistrer Client" :** Ajoute le client au répertoire.
- **Tableau des Clients :**
  - Affiche le **Cumul des achats** en FCFA (qui s'additionne automatiquement à chaque vente en caisse).
  - Affiche le **Statut de Fidélité** (Badge *Standard*, *VIP Silver* ou *VIP Gold*).
  - 🗑️ **Bouton "Supprimer" :** Supprime le client du répertoire.

---

## 12. 🏢 Gestion des Fournisseurs (`fournisseurs.php`)

### 📌 Rôle de cette interface :
Gère vos partenaires grossistes et fournisseurs de smartphones et pièces détachées.

### 🔍 Description des Champs & Boutons :
- **Formulaire d'Ajout :** Nom de la Société (ex: *Dubai Phone Wholesale*), Nom du Contact, Téléphone, Email, Produits fournis (ex: *iPhones neufs certifiés*).
- 🔵 **Bouton "Ajouter Fournisseur" :** Enregistre le partenaire.
- **Tableau des Fournisseurs :** Liste complète avec leurs contacts et bouton de suppression 🗑️.

---

## 13. 🔐 Gestion des Utilisateurs & Rôles (`utilisateurs.php`)

### 📌 Rôle de cette interface :
Réservée aux Administrateurs, elle permet de créer les comptes d'accès pour les employés de la boutique.

### 🔍 Description des Champs & Boutons :
- **Formulaire de Création d'Utilisateur :**
  - **Nom Complet :** Exemple : *Caissier Principal*.
  - **Identifiant de connexion :** Exemple : *vendeur2*.
  - **Mot de passe :** Mot de passe de connexion.
  - **Rôle :**
    - `Admin` : Accès complet à toutes les fonctionnalités et configurations.
    - `Vendeur` : Accès à la Caisse POS, aux Ventes et aux Clients.
    - `Stockiste` : Accès au Catalogue Produits et aux alertes de stock.
- 🔵 **Bouton "Créer Utilisateur" :** Enregistre le nouveau compte.

---

## 💡 Récapitulatif Rapide pour l'Utilisateur au Quotidien

| Action souhaitée | Où aller ? | Bouton à utiliser |
| :--- | :--- | :--- |
| **Vendre un téléphone** | Menu **Caisse / POS** | `Valider la Vente & Éditer la Facture` |
| **Imprimer une facture** | Redirection auto ou **Historique Ventes** | `Imprimer Reçu` |
| **Ajouter un téléphone au stock** | Menu **Produits & Stock** | `+ Nouveau Smartphone` |
| **Modifier le prix ou stock d'un téléphone** | Menu **Produits & Stock** | Icône Crayon ✏️ (`Éditer`) |
| **Prendre un téléphone en réparation** | Menu **SAV Réparations** | `Créer Ticket SAV` |
| **Imprimer un ticket de réparation** | Menu **SAV Réparations** ou ticket auto | `Imprimer` (Icône 🖨️) |
| **Changer l'état d'une réparation** | Menu **SAV Réparations** | Sélecteur Statut (`En Cours`, `Prêt`, `Livré`) |
| **Lancer le tutoriel d'aide** | Menu latéral (Sidebar) | `Visite Guidée (Tutoriel)` 🧭 |

---
*DOCUMENTATION OFFICIELLE MOBILE HOME TOGO - SGC SMARTPHONE V1.0*
