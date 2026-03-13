#  MediaStock – Application de gestion de matériel

> **Projet pédagogique BTS SIO – Option SLAM (Session 2025)**  
> - Réalisation par **Yanis ADIDI**  
> - **Période de réalisation:** 03/10/2025 – 07/11/2025  

---

##  Sommaire

- [Objectif du projet](#-objectif-du-projet)
- [Architecture technique](#️-architecture-technique)
- [Structure du projet](#-structure-du-projet)
- [Environnement Docker](#-environnement-docker)
- [Technologies utilisées](#-technologies-utilisées)
- [Fonctionnalités principales](#-fonctionnalités-principales)
- [Base de données](#-base-de-données)
- [Flux typiques de l’application](#-flux-typiques-de-lapplication)
- [Sécurité & bonnes pratiques](#-sécurité--bonnes-pratiques)
- [Installation et exécution](#-installation-et-exécution)
- [Maintenance et développement](#-maintenance-et-développement)
- [Ressources & Documentation](#-ressources--documentation)
- [Captures et annexes](#-captures-et-annexes)

---

## Objectif du projet

**MediaStock** est une application web mobile-first de **gestion de matériel informatique et audiovisuel**. 
Elle permet notamment :

- la gestion d’un **inventaire de matériel** (ajout, modification, archivage),
- le **suivi des prêts et retours** de matériels,
- l'**identification unique de chaque matériel via QR code unique**,
- **la création et la restitution de prêts** directement depuis un **smartphone** (scan du QR code), 
- **filtrage, recherche et modification** des éléments.

L’application est entièrement **conteneurisée avec Docker**, garantissant un environnement reproductible et portable.  

---

## Architecture technique

Application **MVC léger**, implémentée en **PHP procédural**, avec Docker Compose :

    Client (HTML/CSS/JS/Bootstrap)
                ↓ fetch()
    Serveur PHP (Apache + API REST PHP)
                ↓ PDO
    Base de données MySQL (volume Docker)

### Couches principales :
- **Frontend :** HTML5, CSS3, JavaScript, Bootstrap 5, QRCode.js, Flatpickr  
- **Backend :** PHP 8.2 procédural, PDO
- **Base de données :** MySQL 8.0
- **Administration DB :** phpMyAdmin  
- **Infrastructure :** Docker / Docker Compose (multi-services)

---

## Structure du projet

```
   mediastock/
        │
        ├── docker-compose.yml               # Configuration Docker (développement)
        ├── docker-compose.production.yml    # Configuration pour le déploiement
        ├── Dockerfile                       # Image PHP/Apache avec mod_rewrite
        ├── .env.example / .env              # Variables d’environnement
        │
        ├── sql/
        │   └── init.sql                     # Script SQL (création et données initiales)
        │
        ├── config/
        │   ├── env.example.php / env.php    # variables d'environnement pour Database.php
        │   └── Database.php                 # Connexion PDO centralisée
        │
        ├── public/                          # Dossier exposé par Apache
        │   ├── api/                         # Endpoints backend (CRUD, prêts, restitutions…)
        │   ├── tests/                       # Fichier tests API / intégration
        │   ├── .htaccess                    # Réécriture d'URL et accès frontend
        │   ├── index.php                    # Point d’entrée avec redirection
        │   ├── login.php                    # Authentification d'administrateur
        │   ├── login-verify.php             # Vérification + timeout session
        │   └── frontend/                    # Pages HTML/ CSS/ JS (interface utilisateur)
        │       ├── accueil.html             # Page statique
        │       ├── *.php                    # Pages dynamiques
        │       ├── *.css                    # Feuilles de style
        │       └── *.js                     # Scripts JavaScript (QR code, prêt, retour…)
        │
        └── src/
            └── models/                      # Classes métiers (Item, Pret, Emprunteur, Formation…)

```
Le dossier public/ est le **DocumentRoot Apache**, afin de ne jamais exposer le code métier.

---

##  Environnement Docker

Trois services :

```
    | Service       | Image             | Port local  | Description                |
    |---------------|-------------------|-------------|----------------------------|
    | `web`         | php:8.2-apache    | 9080        | Application PHP            |
    | `mysql`       | mysql:8.0         | interne     | Base de données MySQL      |
    | `phpmyadmin`  | phpmyadmin:latest | 8082        | Interface graphique MySQL  |
```

**Commandes principales :**

```bash
    docker-compose up -d --build       # Construire et lancer les conteneurs
    docker-compose down                # Stopper et supprimer les conteneurs et le réseau
    docker-compose down -v             # Supprimer également les volumes (réinitialisation BDD)
    docker-compose logs -f web         # Logs du serveur PHP
    docker-compose exec web bash       # Entrer dans le conteneur web
```

Les volumes assurent la persistance des données MySQL (mysql-data).

---

## Technologies utilisées

# Frontend
- HTML5 / CSS3
- Bootstrap 5.3.8
- JavaScript (ES6+)
- Flatpickr : sélecteur de dates interactif
- FontAwesome 7.0.1 : icônes
- QRCode.js : génération de QR code
- html5-qrcode.js : scan QR code depuis caméra mobile 

# Backend
- PHP 8.2 (procédural, PDO)
- MySQL 8.0
- Apache 2 mod_rewrite
- Docker / Docker Compose
- PhpMyAdmin 5.2.3

---

## Fonctionnalités principales

**Inventaire (CRUD complet)**
- Ajouter, modifier, supprimer (archiver) un matériel
- Génération automatique et impression d'un QR code unique
- Filtrage et affichage dynamique par catégories, disponibilités et états

**Gestion des prêts et restitutions**
- Enregistrement des prêts par scan QR
- Suivi des dates prévues et retours
- Blocage des prêts si le matériel est déjà emprunté
- Clôture automatique du prêt lors du retour
- Gestion avec état et commentaires

**Gestion des utilisateurs**
- Rôles : étudiant(e) ou intervenant
- Liaison aux formations (sauf intervenants)
- Authentification administrateur (login / session PHP)
- Expiration automatique de session après 5 minutes d'inactivité

**Interface web responsive**
- Adaptée aux smartphones, tablettes et ordinateurs
- Utilisation sur mobile (caméra intégrée pour le scan QR)

**Archivage logique**
- Pas de suppression physique : les items et les emprunteurs sont destinés à être marqués comme `archived = 1` (fonctionnalité prévue, non implémentée pour les emprunteurs dans la version actuelle).

---

## Base de données

La base de données du projet **MediaStock** est modélisée selon la méthode **Merise** (voir cahier des charges et documentation technique).


### Entités principales
- **Item** : matériel (nom, modèle, état, QR code, image, archivage)
- **Categorie** : classification du matériel (informatique, audiovisuel, connectique, etc.)
- **Emprunteur** : personne empruntant du matériel (étudiant ou intervenant)
- **Formation** : rattachement des emprunteurs étudiants
- **Pret** : gestion des prêts et restitutions
- **Administrateur** : gestion et authentification des comptes administrateurs


### Relations clés
- Un **Item** appartient à **une et une seule Categorie** ; une **Categorie** peut regrouper **plusieurs Item**.
- Un **Pret** concerne **un seul Item**, tandis qu’un **Item** peut être associé à **plusieurs Pret successifs**.
- Un **Pret** est effectué par **un seul Emprunteur** ; un **Emprunteur** peut réaliser **plusieurs Pret**.
- Un **Pret** est enregistré par **un seul Administrateur**, qui peut gérer **plusieurs Pret**.
- Un **Emprunteur** peut être rattaché à **zéro ou une Formation**, tandis qu’une **Formation** peut regrouper **plusieurs Emprunteurs** (distinction étudiants / intervenants).*

---

## Flux typiques de l’application

1. L’administrateur se connecte à l’application.
2. Depuis la page Accueil, il peut ajouter, modifier ou supprimer (archiver) un matériel.
3. Lors de l’ajout, il sélectionne la catégorie du matériel et saisit ses caractéristiques.
4. Un QR code unique est automatiquement généré et peut être imprimé.
5. Lors d’un prêt, l’administrateur scanne le QR code du matériel pour enregistrer l’emprunt.
6. À la restitution, le QR code est scanné à nouveau pour clôturer le prêt.
7. L’état et la disponibilité du matériel sont alors mis à jour automatiquement dans la base de données.

---

## Sécurité & bonnes pratiques

- Mots de passe administrateurs ***hachés avec bcrypt***
- Connexions sécurisées via ***HTTPS***
- ***Sessions PHP sécurisées*** (timeout 5 min, redirection automatique)
- Protection contre :
  - ***Injection SQL*** (requêtes préparées PDO)
  - ***XSS*** (htmlspecialchars)
  - ***CSRF*** (token)
- Respect du ***RGPD*** : collecte minimale, suppression après 24 mois
- Accès à la BDD isolé dans Docker (non exposé en production)
- ***Aucune donnée sensible*** dans le dépôt GitHub (.env ignoré).

---

## Installation et exécution

**1. Cloner le projet**

``` bash
    git clone https://github.com/juklau/MediaStock.git
    cd MediaStock
```

**2. Créer le fichier .env**

``` bash
    cp .env.example .env
```

***Modifier les variables :***

``` ini
    DB_NAME=mediastock
    DB_USER=mediastock
    DB_PASSWORD=motdepasse
    DB_ROOT_PASSWORD=rootpass
```

**3. Lancer Docker**

```bash
    docker compose up -d --build
```

**4. Accéder à l'application**

- Application : http://localhost:9080
- PhpMyAdmin : http://localhost:8082

---

## Maintenance et développement

- Arrêter proprement : ***docker-compose stop***
- Redémarrer : ***docker-compose up -d***
- Réinitialiser la BDD : ***docker-compose down -v***
- Accès shell : ***docker-compose exec web bash***

**GitHub Workflow**

```bash
    git add .
    git commit -m "feat: ajout module de restitution"
    git push origin main

```
---

## Améliorations prévues (évolutions futures)

Plusieurs améliorations ont été identifiées pour une version ultérieure de l’application :

- **Recherche avancée par nom d’emprunteur**, en complément des filtres existants.
- **Amélioration de la saisie de la date de retour**, avec la possibilité de définir la date de retour au même jour que la date de début du prêt.
- **Ajout d’un bouton de déconnexion explicite** pour l’administrateur.
- **Gestion multi-utilisateurs**, avec plusieurs comptes administrateurs ou profils distincts.
- **Inventaire par scan de QR code**, permettant d’accéder directement à la fiche d’un matériel.
- **Statistiques et tableaux de bord** : nombre de prêts, retards, matériels les plus empruntés, etc.
- **Journal d’audit / historique des actions** (création, modification, prêt, restitution).
- **Notifications par e-mail** aux emprunteurs en retard pour la restitution du matériel.
- **Gestion avancée des rôles et permissions** (administrateur, technicien, intervenant).
- **Archivage et désarchivage des emprunteurs**, sans suppression de l’historique des prêts.
- **Gestion du droit à l’effacement (RGPD)** : suppression des données sur demande de l’utilisateur (fonctionnalité prévue dans une version ultérieure).

Ces évolutions permettraient d’améliorer l’ergonomie, la sécurité et l’efficacité de l’application dans un contexte de déploiement réel.

---

## Ressources & Documentation

- Cahier des charges du projet,
- Cours de développement web (PHP/MySQL/Bootstrap),
- Documentation officielle de PHP, PDO, Bootstrap, Docker et Flatpickr,
- Tutoriels sur QR code en JS,
- Exemples de code et dossiers “mediastock_backend” fournis par le professeur.
- [Maquettes Figma ](https://www.figma.com/design/8YYwxKWra3P9QWC6UJBv2L/Untitled?node-id=1-3&t=VbQzFZxMR3Aizp1A-0)

---

## Captures et annexes

- [Maquettes Figma ](https://www.figma.com/design/8YYwxKWra3P9QWC6UJBv2L/Untitled?node-id=1-3&t=VbQzFZxMR3Aizp1A-0)
- Schémas de base de données :
  - MCD et MLD (page 18 du cahier des charges)
- Diagrammes de Gantt et Kanban (pages 20–21 du cahier des charges)


© 2025 MediaStock – Projet étudiant BTS SIO SLAM
# MediaStock
