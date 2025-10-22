<?php
/**
 * Test du modèle Emprunteur (Borrower)
 * 
 * Ce script teste les fonctionnalités du modèle Emprunteur, notamment:
 * - Récupération de tous les emprunteurs
 * - Récupération d'un emprunteur par ID
 * - Récupération des emprunteurs avec leurs informations de formation
 * - Recherche d'emprunteurs par nom ou prénom
 * - Récupération des emprunteurs par formation
 * - Récupération des emprunteurs par rôle
 * - Récupération des prêts actifs d'un emprunteur
 * - Récupération de l'historique des prêts d'un emprunteur
 */

// Inclusion de l'autoloader pour charger automatiquement les classes
require_once '../autoload.php';

// Titre de la page
echo "<h1>Test du modèle Emprunteur (Borrower)</h1>";

// Création d'une instance du modèle Emprunteur
$emprunteurModel = new Models\Emprunteur();

// Test 1: Récupération de tous les emprunteurs
echo "<h2>Test 1: Récupération de tous les emprunteurs</h2>";
echo "<p>Cette fonction récupère tous les emprunteurs de la base de données.</p>";

// Exécution de la méthode getAll() qui retourne tous les emprunteurs
$emprunteurs = $emprunteurModel->getAll();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre d'emprunteurs trouvés: " . count($emprunteurs) . "</p>";
echo "<pre>";
// Affichage des 5 premiers emprunteurs seulement pour éviter une sortie trop longue
$displayEmprunteurs = array_slice($emprunteurs, 0, 5);
print_r($displayEmprunteurs);
if (count($emprunteurs) > 5) {
    echo "... (et " . (count($emprunteurs) - 5) . " autres emprunteurs)";
}
echo "</pre>";

// Test 2: Récupération d'un emprunteur par ID
echo "<h2>Test 2: Récupération d'un emprunteur par ID</h2>";
echo "<p>Cette fonction récupère un emprunteur spécifique par son ID.</p>";

// Vérification qu'il y a au moins un emprunteur dans la base de données
if (count($emprunteurs) > 0) {
    // Récupération du premier ID d'emprunteur
    $emprunteurId = $emprunteurs[0]['id'];
    
    // Exécution de la méthode getById() pour récupérer l'emprunteur
    $emprunteur = $emprunteurModel->getById($emprunteurId);
    
    // Affichage des résultats
    echo "<h3>Résultats pour l'emprunteur avec ID $emprunteurId:</h3>";
    echo "<pre>";
    print_r($emprunteur);
    echo "</pre>";
} else {
    echo "<p>Aucun emprunteur trouvé dans la base de données.</p>";
}

// Test 3: Récupération des emprunteurs avec leurs informations de formation
echo "<h2>Test 3: Récupération des emprunteurs avec leurs informations de formation</h2>";
echo "<p>Cette fonction récupère tous les emprunteurs avec les informations de leur formation.</p>";

// Exécution de la méthode getAllWithFormation() qui retourne tous les emprunteurs avec leur formation
$emprunteursWithFormation = $emprunteurModel->getAllWithFormation();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre d'emprunteurs trouvés: " . count($emprunteursWithFormation) . "</p>";
echo "<pre>";
// Affichage des 5 premiers emprunteurs seulement pour éviter une sortie trop longue
$displayEmprunteurs = array_slice($emprunteursWithFormation, 0, 5);
print_r($displayEmprunteurs);
if (count($emprunteursWithFormation) > 5) {
    echo "... (et " . (count($emprunteursWithFormation) - 5) . " autres emprunteurs)";
}
echo "</pre>";

// Test 4: Recherche d'emprunteurs par nom ou prénom
echo "<h2>Test 4: Recherche d'emprunteurs par nom ou prénom</h2>";
echo "<p>Cette fonction recherche des emprunteurs dont le nom ou le prénom contient un terme spécifique.</p>";

// Terme de recherche (à adapter selon votre base de données)
// Utilisation d'un terme générique qui a des chances de trouver des résultats
$searchTerm = 'a';

// Exécution de la méthode searchByName() pour rechercher des emprunteurs
$searchResults = $emprunteurModel->searchByName($searchTerm);

// Affichage des résultats
echo "<h3>Résultats de la recherche pour le terme '$searchTerm':</h3>";
echo "<p>Nombre d'emprunteurs trouvés: " . count($searchResults) . "</p>";
if (count($searchResults) > 0) {
    echo "<pre>";
    // Affichage des 5 premiers résultats seulement pour éviter une sortie trop longue
    $displayResults = array_slice($searchResults, 0, 5);
    print_r($displayResults);
    if (count($searchResults) > 5) {
        echo "... (et " . (count($searchResults) - 5) . " autres emprunteurs)";
    }
    echo "</pre>";
} else {
    echo "<p>Aucun emprunteur trouvé pour ce terme de recherche.</p>";
}

// Test 5: Récupération des emprunteurs par formation
echo "<h2>Test 5: Récupération des emprunteurs par formation</h2>";
echo "<p>Cette fonction récupère tous les emprunteurs appartenant à une formation spécifique.</p>";

// Récupération d'une instance du modèle Formation pour obtenir une formation existante
$formationModel = new Models\Formation();
$formations = $formationModel->getAll();

// Vérification qu'il y a au moins une formation dans la base de données
if (count($formations) > 0) {
    // Récupération du premier ID de formation
    $formationId = $formations[0]['id'];
    
    // Exécution de la méthode getByFormation() pour récupérer les emprunteurs de cette formation
    $emprunteursByFormation = $emprunteurModel->getByFormation($formationId);
    
    // Affichage des résultats
    echo "<h3>Emprunteurs de la formation avec ID $formationId:</h3>";
    echo "<p>Nombre d'emprunteurs trouvés: " . count($emprunteursByFormation) . "</p>";
    if (count($emprunteursByFormation) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers emprunteurs seulement pour éviter une sortie trop longue
        $displayEmprunteurs = array_slice($emprunteursByFormation, 0, 5);
        print_r($displayEmprunteurs);
        if (count($emprunteursByFormation) > 5) {
            echo "... (et " . (count($emprunteursByFormation) - 5) . " autres emprunteurs)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun emprunteur trouvé pour cette formation.</p>";
    }
} else {
    echo "<p>Aucune formation trouvée dans la base de données.</p>";
}

// Test 6: Récupération des emprunteurs par rôle
echo "<h2>Test 6: Récupération des emprunteurs par rôle</h2>";
echo "<p>Cette fonction récupère tous les emprunteurs ayant un rôle spécifique (étudiant(e) ou intervenant).</p>";

// Rôle à rechercher
$role = 'etudiant(e)';

// Exécution de la méthode getByRole() pour récupérer les emprunteurs avec ce rôle
$emprunteursByRole = $emprunteurModel->getByRole($role);

// Affichage des résultats
echo "<h3>Emprunteurs avec le rôle '$role':</h3>";
echo "<p>Nombre d'emprunteurs trouvés: " . count($emprunteursByRole) . "</p>";
if (count($emprunteursByRole) > 0) {
    echo "<pre>";
    // Affichage des 5 premiers emprunteurs seulement pour éviter une sortie trop longue
    $displayEmprunteurs = array_slice($emprunteursByRole, 0, 5);
    print_r($displayEmprunteurs);
    if (count($emprunteursByRole) > 5) {
        echo "... (et " . (count($emprunteursByRole) - 5) . " autres emprunteurs)";
    }
    echo "</pre>";
} else {
    echo "<p>Aucun emprunteur trouvé avec ce rôle.</p>";
}

// Test 7: Récupération des prêts actifs d'un emprunteur
echo "<h2>Test 7: Récupération des prêts actifs d'un emprunteur</h2>";
echo "<p>Cette fonction récupère tous les prêts actifs (non retournés) d'un emprunteur spécifique.</p>";

// Vérification qu'il y a au moins un emprunteur dans la base de données
if (count($emprunteurs) > 0) {
    // Récupération du premier ID d'emprunteur
    $emprunteurId = $emprunteurs[0]['id'];
    
    // Exécution de la méthode getActiveLoans() pour récupérer les prêts actifs de cet emprunteur
    $activeLoans = $emprunteurModel->getActiveLoans($emprunteurId);
    
    // Affichage des résultats
    echo "<h3>Prêts actifs de l'emprunteur avec ID $emprunteurId:</h3>";
    echo "<p>Nombre de prêts actifs: " . count($activeLoans) . "</p>";
    if (count($activeLoans) > 0) {
        echo "<pre>";
        print_r($activeLoans);
        echo "</pre>";
    } else {
        echo "<p>Aucun prêt actif trouvé pour cet emprunteur.</p>";
    }
} else {
    echo "<p>Aucun emprunteur trouvé dans la base de données.</p>";
}

// Test 8: Récupération de l'historique des prêts d'un emprunteur
echo "<h2>Test 8: Récupération de l'historique des prêts d'un emprunteur</h2>";
echo "<p>Cette fonction récupère tous les prêts (actifs et terminés) d'un emprunteur spécifique.</p>";

// Vérification qu'il y a au moins un emprunteur dans la base de données
if (count($emprunteurs) > 0) {
    // Récupération du premier ID d'emprunteur
    $emprunteurId = $emprunteurs[0]['id'];
    
    // Exécution de la méthode getLoanHistory() pour récupérer l'historique des prêts de cet emprunteur
    $loanHistory = $emprunteurModel->getLoanHistory($emprunteurId);
    
    // Affichage des résultats
    echo "<h3>Historique des prêts de l'emprunteur avec ID $emprunteurId:</h3>";
    echo "<p>Nombre de prêts trouvés: " . count($loanHistory) . "</p>";
    if (count($loanHistory) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
        $displayLoans = array_slice($loanHistory, 0, 5);
        print_r($displayLoans);
        if (count($loanHistory) > 5) {
            echo "... (et " . (count($loanHistory) - 5) . " autres prêts)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun prêt trouvé pour cet emprunteur.</p>";
    }
} else {
    echo "<p>Aucun emprunteur trouvé dans la base de données.</p>";
}

// Test 9: Création d'un nouvel emprunteur (commenté pour éviter de modifier la base de données)
echo "<h2>Test 9: Création d'un nouvel emprunteur</h2>";
echo "<p>Cette fonction crée un nouvel emprunteur dans la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la création d'un emprunteur.</p>";

/*
// Vérification qu'il y a au moins une formation dans la base de données
if (count($formations) > 0) {
    // Récupération du premier ID de formation
    $formationId = $formations[0]['id'];
    
    // Données pour le nouvel emprunteur
    $newEmprunteurData = [
        'emprunteur_nom' => 'Test Nom ' . time(),
        'emprunteur_prenom' => 'Test Prénom',
        'role' => 'etudiant(e)',
        'formation_id' => $formationId
    ];
    
    // Exécution de la méthode create() pour créer un nouvel emprunteur
    $newEmprunteurId = $emprunteurModel->create($newEmprunteurData);
    
    // Affichage des résultats
    echo "<h3>Résultats de la création:</h3>";
    if ($newEmprunteurId) {
        echo "<p>Nouvel emprunteur créé avec succès. ID: $newEmprunteurId</p>";
        
        // Récupération du nouvel emprunteur pour vérification
        $newEmprunteur = $emprunteurModel->getById($newEmprunteurId);
        echo "<pre>";
        print_r($newEmprunteur);
        echo "</pre>";
    } else {
        echo "<p>Échec de la création de l'emprunteur.</p>";
    }
} else {
    echo "<p>Aucune formation trouvée dans la base de données pour associer à l'emprunteur.</p>";
}
*/

// Lien pour revenir à la page d'accueil des tests
echo "<p><a href='index.php'>Retour à la page d'accueil des tests</a></p>";