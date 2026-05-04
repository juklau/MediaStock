# 📋 CAHIER DES CHARGES — PROJET MEDIASTOCK

**BTS SIO — Option SLAM (Solutions Logicielles et Applications Métiers)**
*Référentiel BTS Services Informatiques aux Organisations*

---

## 1. PRÉSENTATION GÉNÉRALE

### 1.1 Contexte du projet
Dans le cadre du BTS SIO option SLAM, le projet **MediaStock** répond à un besoin réel de gestion d'un parc de matériel informatique et audiovisuel. L'organisation cliente dispose d'un inventaire conséquent (PC portables, Apple TV, micros Sennheiser, équipements pédagogiques) prêté régulièrement à des formateurs et apprenants.

### 1.2 Maîtrise d'ouvrage / Maîtrise d'œuvre
- **MOA** : Établissement / Service Formation
- **MOE** : Étudiant développeur BTS SIO SLAM

### 1.3 Objectifs
- ✅ Centraliser l'inventaire de matériel multimédia.
- ✅ Tracer les prêts et les retours de manière fiable.
- ✅ Accélérer les opérations terrain via le **scan de QR Code**.
- ✅ Assurer la sécurité des accès via **Docker + Traefik (HTTPS)**.

---

## 2. ÉTUDE DU BESOIN

### 2.1 Besoins fonctionnels
- Authentification administrateur.
- CRUD complet sur le matériel.
- Enregistrement d'un prêt/retour par scan QR.
- Historique des prêts.

---

## 3. SPÉCIFICATIONS TECHNIQUES
- Backend : **PHP 8.2** (PDO)
- Frontend : **Bootstrap 5.3**
- Infrastructure : **Docker** / **Traefik**
