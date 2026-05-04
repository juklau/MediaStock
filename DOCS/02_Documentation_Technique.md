# 🛠️ DOCUMENTATION TECHNIQUE — MEDIASTOCK

**Application de gestion d'inventaire multimédia**
*BTS SIO SLAM — Document à destination des développeurs et administrateurs système*

---

## 1. ARCHITECTURE GÉNÉRALE

### 1.1 Vue d'ensemble
MediaStock est une application Web **3-tiers** conteneurisée :

```
┌────────────┐   HTTPS:4433    ┌─────────┐    HTTP    ┌──────────────┐
│ Navigateur │ ───────────────►│ Traefik │ ──────────►│ Conteneur web│
│ (mobile/PC)│                 │ (proxy) │            │  PHP 8.2     │
└────────────┘                 └─────────┘            └──────┬───────┘
                                                              │ PDO
                                                      ┌──────▼──────┐
                                                      │ Conteneur db│
                                                      │ MySQL 8     │
                                                      └─────────────┘
```

### 1.2 Services Docker

| Service | Image | Rôle | Port interne |
|---------|-------|------|--------------|
| `web` | `php:8.2-apache` | Serveur applicatif PHP | 80 |
| `db` | `mysql:8` | Base de données | 3306 |
| `phpmyadmin` | `phpmyadmin:latest` | Administration BDD (auth manuelle) | 80 |

### 1.3 Réseau de production
- Exposition publique via **Traefik** sur `https://mediastock.iris.a3n.fr:4433`
- Redirection HTTP → HTTPS forcée
- Certificat TLS géré par Traefik

---

## 2. ARBORESCENCE DU PROJET

```
mediastock/
├── docker-compose.yml
├── Dockerfile
├── config/
│   ├── Database.php       ← ⚠ Majuscule obligatoire (Linux case-sensitive)
│   └── env.php            ← Pont Docker → PHP (retourne un tableau de config)
├── sql/
│   └── init.sql           ← Schéma + 25 matériels + 16 formations + 4 catégories
├── src/
│   └── models/
│       ├── BaseModel.php
│       ├── Item.php
│       ├── Pret.php
│       └── Administrateur.php
└── public/
    ├── index.php          ← Point d'entrée (redirection vers frontend)
    ├── login.php          ← Traitement de l'authentification (API)
    ├── api/               ← Endpoints JSON
    │   ├── gettoutItems.php
    │   ├── addpret.php
    │   ├── findbyqrcode.php
    │   └── ...
    └── frontend/          ← IHM (HTML, CSS, JS, html5-qrcode)
```

---

## 3. BASE DE DONNÉES

### 3.1 MCD (Modèle Conceptuel de Données)

```
ADMINISTRATEUR (id, login, mot_de_passe_hash)

CATEGORIE (id, categorie)
   1,n ──posséder── 0,n ITEM

FORMATION (id, formation)
   0,n ──concerner── 0,n EMPRUNTEUR

ITEM (id, nom, model, qr_code, image_url, etat, id_categorie, archived)
   1,1 ──faire l'objet── 0,n PRET

PRET (id, item_id, emprunteur_id, preteur_id, date_sortie, 
      date_retour_prevue, date_retour_effective, note_debut, note_fin)
```

### 3.2 Données initiales (`sql/init.sql`)
- **25 matériels réels** : PC DELL, Apple TV 4K, micros Sennheiser, etc.
- **16 formations** (ECS, NSS, PSL, Iris)
- **4 catégories** : Informatique, Audio, Connectique, Autres
- **1 administrateur** : `admin` / `MediaStock_06*`

### 3.3 Accès
| Élément | Valeur |
|---------|--------|
| Hôte (interne) | `mysql` |
| Mot de passe BDD | `MediaStock06200@@&` |
| **Login App** | `admin` / `MediaStock_06*` |

---

## 4. CONFIGURATION

### 4.1 `config/env.php`
Récupère les variables d'environnement injectées par Docker :
```php
return [
    'DB_HOST'     => getenv('DB_HOST') ?: 'mysql',
    'DB_NAME'     => getenv('DB_NAME') ?: 'mediastock',
    'DB_USER'     => getenv('DB_USER') ?: 'mediastock',
    'DB_PASSWORD' => getenv('DB_PASSWORD') ?: 'changeme'
];
```

### 4.2 `config/Database.php` (Singleton PDO)
Utilise le namespace `Config` et le fichier `env.php` pour établir une connexion unique et sécurisée.

---

## 5. API INTERNE (`/public/api`)

| Endpoint | Méthode | Description | Auth |
|----------|---------|-------------|------|
| `/login.php` | POST | Connexion administrateur (session) | ❌ |
| `/api/gettoutItems.php` | GET | Liste complète du matériel | ✅ |
| `/api/additem.php` | POST | Création d'un nouveau matériel | ✅ |
| `/api/updateitem.php` | POST | Mise à jour d'un matériel | ✅ |
| `/api/addpret.php` | POST | Enregistrer un nouveau prêt | ✅ |
| `/api/cloturepret.php` | POST | Enregistrer un retour (restitution) | ✅ |
| `/api/findbyqrcode.php?qr_code=X` | GET | Récupère un matériel par QR Code | ✅ |

---

## 6. SÉCURITÉ

- **Anti-injection SQL** : Utilisation systématique de PDO avec requêtes préparées.
- **Anti-XSS** : Nettoyage des entrées et échappement des sorties (`htmlspecialchars`).
- **Hashage** : Mots de passe stockés via `password_hash()` (Bcrypt).
- **HTTPS** : Flux sécurisé sur le port 4433 géré par Traefik.
- **phpMyAdmin** : Connexion automatique désactivée pour exiger une authentification MySQL.

---

## 7. DÉPLOIEMENT

```bash
# Lancement de l'infrastructure
docker-compose -f docker-compose.production.yml up -d --build
```

---

*Document mis à jour le 4 mai 2026.*
