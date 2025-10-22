<?php
/**
 * Test du modèle Administrateur
 * 
 * Ce script teste les fonctionnalités du modèle Administrateur, notamment:
 * - Récupération de tous les administrateurs
 * - Récupération d'un administrateur par ID
 * - Authentification d'un administrateur
 * - Création d'un nouvel administrateur
 * - Mise à jour du mot de passe d'un administrateur
 * - Récupération des prêts gérés par un administrateur
 */

// Inclusion de l'autoloader pour charger automatiquement les classes
require_once '../autoload.php';

// Titre de la page
echo "<h1>Test du modèle Administrateur</h1>";

// Création d'une instance du modèle Administrateur
$adminModel = new Models\Administrateur();

// Test 1: Récupération de tous les administrateurs
echo "<h2>Test 1: Récupération de tous les administrateurs</h2>";
echo "<p>Cette fonction récupère tous les administrateurs de la base de données sans exposer leurs mots de passe.</p>";

// Exécution de la méthode getAllSecure() qui retourne tous les administrateurs sans les mots de passe
$admins = $adminModel->getAllSecure();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<pre>";
print_r($admins);
echo "</pre>";

// Test 2: Récupération d'un administrateur par ID
echo "<h2>Test 2: Récupération d'un administrateur par ID</h2>";
echo "<p>Cette fonction récupère un administrateur spécifique par son ID.</p>";

// Vérification qu'il y a au moins un administrateur dans la base de données
if (count($admins) > 0) {
    // Récupération du premier ID d'administrateur
    $adminId = $admins[0]['id'];
    
    // Exécution de la méthode getById() pour récupérer l'administrateur
    $admin = $adminModel->getById($adminId);
    
    // Affichage des résultats
    echo "<h3>Résultats pour l'administrateur avec ID $adminId:</h3>";
    echo "<pre>";
    // Masquage du mot de passe pour la sécurité
    if (isset($admin['mot_de_passe_hash'])) {
        $admin['mot_de_passe_hash'] = '********';
    }
    print_r($admin);
    echo "</pre>";
} else {
    echo "<p>Aucun administrateur trouvé dans la base de données.</p>";
}

// Test 3: Authentification d'un administrateur
echo "<h2>Test 3: Authentification d'un administrateur</h2>";
echo "<p>Cette fonction vérifie les identifiants d'un administrateur.</p>";
echo "<p><strong>Note:</strong> Ce test utilise des identifiants prédéfinis. Dans un environnement réel, les identifiants seraient fournis par un formulaire.</p>";

// Identifiants de test (à adapter selon votre base de données)
$login = 'admin';
$password = 'L0veMedi45ch00l'; // Mot de passe incorrect pour éviter de modifier la base de données

// Exécution de la méthode authenticate() pour vérifier les identifiants
$authResult = $adminModel->authenticate($login, $password);

// Affichage des résultats
echo "<h3>Résultats de l'authentification:</h3>";
if ($authResult) {
    echo "<p>Authentification réussie pour l'administrateur: {$authResult['login']}</p>";
    echo "<pre>";
    print_r($authResult);
    echo "</pre>";
} else {
    echo "<p>Échec de l'authentification. Identifiants incorrects.</p>";
}

// Test 4: Création d'un nouvel administrateur (commenté pour éviter de modifier la base de données)
echo "<h2>Test 4: Création d'un nouvel administrateur</h2>";
echo "<p>Cette fonction crée un nouvel administrateur dans la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la création d'un administrateur.</p>";

/*
// Données pour le nouvel administrateur
$newAdminLogin = 'test_admin_' . time(); // Utilisation de l'horodatage pour garantir l'unicité
$newAdminPassword = 'test_password';

// Exécution de la méthode createAdmin() pour créer un nouvel administrateur
$newAdminId = $adminModel->createAdmin($newAdminLogin, $newAdminPassword);

// Affichage des résultats
echo "<h3>Résultats de la création:</h3>";
if ($newAdminId) {
    echo "<p>Nouvel administrateur créé avec succès. ID: $newAdminId</p>";
    
    // Récupération du nouvel administrateur pour vérification
    $newAdmin = $adminModel->getById($newAdminId);
    echo "<pre>";
    // Masquage du mot de passe pour la sécurité
    if (isset($newAdmin['mot_de_passe_hash'])) {
        $newAdmin['mot_de_passe_hash'] = '********';
    }
    print_r($newAdmin);
    echo "</pre>";
} else {
    echo "<p>Échec de la création de l'administrateur. Le login existe peut-être déjà.</p>";
}
*/

// Test 5: Mise à jour du mot de passe d'un administrateur (commenté pour éviter de modifier la base de données)
echo "<h2>Test 5: Mise à jour du mot de passe d'un administrateur</h2>";
echo "<p>Cette fonction met à jour le mot de passe d'un administrateur existant.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la mise à jour du mot de passe.</p>";

/*
// Vérification qu'il y a au moins un administrateur dans la base de données
if (count($admins) > 0) {
    // Récupération du premier ID d'administrateur
    $adminId = $admins[0]['id'];
    
    // Nouveau mot de passe
    $newPassword = 'new_password_' . time();
    
    // Exécution de la méthode updatePassword() pour mettre à jour le mot de passe
    $updateResult = $adminModel->updatePassword($adminId, $newPassword);
    
    // Affichage des résultats
    echo "<h3>Résultats de la mise à jour:</h3>";
    if ($updateResult) {
        echo "<p>Mot de passe mis à jour avec succès pour l'administrateur avec ID $adminId.</p>";
    } else {
        echo "<p>Échec de la mise à jour du mot de passe.</p>";
    }
} else {
    echo "<p>Aucun administrateur trouvé dans la base de données.</p>";
}
*/

// Test 6: Récupération des prêts gérés par un administrateur
echo "<h2>Test 6: Récupération des prêts gérés par un administrateur</h2>";
echo "<p>Cette fonction récupère tous les prêts gérés par un administrateur spécifique.</p>";

// Vérification qu'il y a au moins un administrateur dans la base de données
if (count($admins) > 0) {
    // Récupération du premier ID d'administrateur
    $adminId = $admins[0]['id'];
    
    // Exécution de la méthode getAdminLoans() pour récupérer les prêts
    $loans = $adminModel->getAdminLoans($adminId);
    
    // Affichage des résultats
    echo "<h3>Prêts gérés par l'administrateur avec ID $adminId:</h3>";
    if (count($loans) > 0) {
        echo "<pre>";
        print_r($loans);
        echo "</pre>";
    } else {
        echo "<p>Aucun prêt trouvé pour cet administrateur.</p>";
    }
} else {
    echo "<p>Aucun administrateur trouvé dans la base de données.</p>";
}

// Lien pour revenir à la page d'accueil des tests
echo "<p><a href='index.php'>Retour à la page d'accueil des tests</a></p>";