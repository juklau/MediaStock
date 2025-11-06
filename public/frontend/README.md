# 🎬 MediaStock – Application de gestion de matériel

> **Projet BTS SIO – Option SLAM (Session 2025)**  
> - Réalisation de **JUHASZ Klaudia**  
> - **Période de réalisation:** 03/10/2025 – 07/11/2025  

---

## 📖 Sommaire

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

## 🎯 Objectif du projet

**MediaStock** est une application web de **gestion de matériel informatique et audiovisuel**. 
Elle permet à une organisation (ex. établissement scolaire, entreprise) de :

- gérer un **inventaire de matériel** (ajout, modification, archivage),
- **suivre les prêts et retours** de matériels par les utilisateurs,
- **identifier chaque matériel via un QR code unique**,
- permettre la **création et la restitution de prêts** directement depuis un **smartphone** (scan du QR code).

L’application est **conteneurisée avec Docker** pour garantir un environnement stable et portable.  

---

## ⚙️ Architecture technique

Application **MVC simplifié** en **PHP procédural**, conteneurisée avec Docker Compose :

    Client (HTML/CSS/JS/Bootstrap)
                ↓ fetch()
    Serveur PHP (Apache + API REST PHP)
                ↓ PDO
    Base MySQL (Docker volume persistant)

### Couches principales :
- **Frontend :** HTML5, CSS3, JavaScript, Bootstrap 5, QRCode.js, Flatpickr  
- **Backend :** PHP procédural (API REST légère, PDO)
- **BDD :** MySQL via conteneur Docker
- **Admin DB :** phpMyAdmin  
- **Infrastructure :** Docker Compose (multi-services)

---

## 📦 Structure du projet

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
        │   └── database.php                 # Connexion PDO centralisée
        │
        ├── public/                          # Dossier exposé par Apache
        │   ├── api/                         # Endpoints backend (CRUD, prêts, restitutions…)
        │   ├── tests/                       # Fichier tests API / intégration
        │   ├── .htaccess                    # Réécriture d'URL vers index.php
        │   ├── index.php                    # Point d’entrée (routeur)
        │   ├── login.php                    # Authentification d'administrateur
        │   └── frontend/                    # Pages HTML/ CSS/ JS (interface utilisateur)
        │       ├── acceuil.html             # Page statique
        │       ├── *.php                    # Pages dynamiques
        │       ├── *.css                    # Feuilles de style
        │       └── *.js                     # Scripts JavaScript (QR code, prêt, retour…)
        │
        └── src/
            └── models/                      # Classes métiers (Item, Pret, Emprunteur, Formation…)

```

---

## 🐳 Environnement Docker

Trois services :

```
    | Service       | Image             | Port local  | Description                |
    |---------------|-------------------|-------------|----------------------------|
    | `web`         | php:8.2-apache    | 8080        | Serveur PHP + Apache       |
    | `mysql`       | mysql:8.0         | interne     | Base de données MySQL      |
    | `phpmyadmin`  | phpmyadmin:latest | 8081        | Interface de gestion MySQL |
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

## 🧩 Technologies utilisées

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

## 💻 Fonctionnalités principales

**Inventaire (CRUD complet)**
- Ajouter, modifier, supprimer, archiver un matériel
- Génération automatique et impression d'un QR code unique
- Filtrage et affichage dynamique par catégories

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

**Interface web responsive**
- Adaptée aux smartphones, tablettes et ordinateurs
- Utilisation sur mobile (caméra intégrée pour le scan QR)

**Archivage logique**
- Pas de suppression physique : items ou emprunteurs marqués comme `archived = 1`

---

## 🧠 Base de données

Schéma modélisé sous Merise (cf. cahier des charges).

# Entités principales :
- Item – matériel (nom, modèle, état, QR code, catégorie)
- Categorie - informatique/ audio/ connectique/ autres 
- Formation – regroupement d’emprunteurs
- Emprunteur – étudiant ou intervenant
- Pret – gestion des prêts
- Administrateur – gestion du système

# Relations clés :
- Un ***Emprunteur*** appartient à une ***Formation***
- Un ***Pret*** relie un ***Item***, un ***Emprunteur***, et un ***Administrateur***
- Un ***Item*** appartient à une ***Categorie***

---

## Flux typiques de l’application

1. L’administrateur se connecte à l’application.
2. Il sélectionne une catégorie → ajoute ou modifie un matériel.
3. Le QR code est généré et imprimable.
4. L'administrateur scanne le QR code pour créer un prêt.
5. À la restitution, le matériel est scanné à nouveau → prêt clôturé.
6. L’état et la disponibilité sont mis à jour automatiquement.

---

## Sécurité & bonnes pratiques

- Mots de passe administrateurs ***hachés avec bcrypt***
- Connexions sécurisées via ***HTTPS***
- Protection contre :
  - ***Injection SQL*** (requêtes préparées PDO)
  - ***XSS*** (htmlspecialchars)
  - ***CSRF*** (token)
- Respect du ***RGPD*** : collecte minimale, suppression après 24 mois
- Accès à la BDD isolé dans Docker (non exposé en production)

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

- Application : http://localhost:8080
- PhpMyAdmin : http://localhost:8081

---

## Maintenance et développement

- Arrêter proprement : ***docker compose stop***
- Redémarrer : ***docker compose up -d***
- Réinitialiser la BDD : ***docker compose down -v***
- Accès shell : ***docker compose exec web bash***

**GitHub Workflow**

```bash
    git add .
    git commit -m "feat: ajout module de restitution"
    git push origin main

```

---

## Ressources & Documentation

- Cahier des charges du projet,
- Cours de développement web (PHP/MySQL/Bootstrap),
- Documentation officielle de PHP, PDO, Bootstrap, Docker et Flatpickr,
- Tutoriels sur QR code en JS,
- Exemples de code et dossiers “mediastock_backend” fournis par le professeur.

---

## Captures et 

- [Maquettes Figma ](https://www.figma.com/design/8YYwxKWra3P9QWC6UJBv2L/Untitled?node-id=1-3&t=VbQzFZxMR3Aizp1A-0)
- Schémas de base de données :
 - MCD et MLD (page 19 du cahier des charges)
 - Diagrammes de Gantt et Kanban (pages 21–22 du cahier des charges)


© 2025 MediaStock – Projet étudiant BTS SIO SLAM
