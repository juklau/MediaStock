<?php
/**
 * Test du modèle Formation
 * 
 * Ce script teste les fonctionnalités du modèle Formation, notamment:
 * - Récupération de toutes les formations
 * - Récupération d'une formation par ID
 * - Récupération des formations avec le nombre d'emprunteurs dans chacune
 * - Récupération des emprunteurs d'une formation
 * - Récupération des emprunteurs d'une formation avec détails
 * - Récupération des statistiques de prêts par formation
 */

// Inclusion de l'autoloader pour charger automatiquement les classes
require_once '../autoload.php';

// Titre de la page
echo "<h1>Test du modèle Formation</h1>";

// Création d'une instance du modèle Formation
$formationModel = new Models\Formation();

// Test 1: Récupération de toutes les formations
echo "<h2>Test 1: Récupération de toutes les formations</h2>";
echo "<p>Cette fonction récupère toutes les formations de la base de données.</p>";

// Exécution de la méthode getAll() qui retourne toutes les formations
$formations = $formationModel->getAll();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de formations trouvées: " . count($formations) . "</p>";
echo "<pre>";
print_r($formations);
echo "</pre>";

// Test 2: Récupération d'une formation par ID
echo "<h2>Test 2: Récupération d'une formation par ID</h2>";
echo "<p>Cette fonction récupère une formation spécifique par son ID.</p>";

// Vérification qu'il y a au moins une formation dans la base de données
if (count($formations) > 0) {
    // Récupération du premier ID de formation
    $formationId = $formations[0]['id'];
    
    // Exécution de la méthode getById() pour récupérer la formation
    $formation = $formationModel->getById($formationId);
    
    // Affichage des résultats
    echo "<h3>Résultats pour la formation avec ID $formationId:</h3>";
    echo "<pre>";
    print_r($formation);
    echo "</pre>";
} else {
    echo "<p>Aucune formation trouvée dans la base de données.</p>";
}

// Test 3: Récupération des formations avec le nombre d'emprunteurs dans chacune
echo "<h2>Test 3: Récupération des formations avec le nombre d'emprunteurs</h2>";
echo "<p>Cette fonction récupère toutes les formations avec le nombre d'emprunteurs dans chacune.</p>";

// Exécution de la méthode getAllWithBorrowerCount() qui retourne toutes les formations avec le nombre d'emprunteurs
$formationsWithBorrowerCount = $formationModel->getAllWithBorrowerCount();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de formations trouvées: " . count($formationsWithBorrowerCount) . "</p>";
echo "<pre>";
print_r($formationsWithBorrowerCount);
echo "</pre>";

// Test 4: Récupération des emprunteurs d'une formation
echo "<h2>Test 4: Récupération des emprunteurs d'une formation</h2>";
echo "<p>Cette fonction récupère tous les emprunteurs appartenant à une formation spécifique.</p>";

// Vérification qu'il y a au moins une formation dans la base de données
if (count($formations) > 0) {
    // Récupération du premier ID de formation
    $formationId = $formations[0]['id'];
    
    // Exécution de la méthode getFormationBorrowers() pour récupérer les emprunteurs de cette formation
    $formationBorrowers = $formationModel->getFormationBorrowers($formationId);
    
    // Affichage des résultats
    echo "<h3>Emprunteurs de la formation avec ID $formationId:</h3>";
    echo "<p>Nombre d'emprunteurs trouvés: " . count($formationBorrowers) . "</p>";
    if (count($formationBorrowers) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers emprunteurs seulement pour éviter une sortie trop longue
        $displayBorrowers = array_slice($formationBorrowers, 0, 5);
        print_r($displayBorrowers);
        if (count($formationBorrowers) > 5) {
            echo "... (et " . (count($formationBorrowers) - 5) . " autres emprunteurs)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun emprunteur trouvé pour cette formation.</p>";
    }
} else {
    echo "<p>Aucune formation trouvée dans la base de données.</p>";
}

// Test 5: Récupération des emprunteurs d'une formation avec détails
echo "<h2>Test 5: Récupération des emprunteurs d'une formation avec détails</h2>";
echo "<p>Cette fonction récupère tous les emprunteurs appartenant à une formation spécifique avec des détails supplémentaires comme le nombre de prêts actifs.</p>";

// Vérification qu'il y a au moins une formation dans la base de données
if (count($formations) > 0) {
    // Récupération du premier ID de formation
    $formationId = $formations[0]['id'];
    
    // Exécution de la méthode getFormationBorrowersWithDetails() pour récupérer les emprunteurs de cette formation avec détails
    $formationBorrowersWithDetails = $formationModel->getFormationBorrowersWithDetails($formationId);
    
    // Affichage des résultats
    echo "<h3>Emprunteurs de la formation avec ID $formationId (avec détails):</h3>";
    echo "<p>Nombre d'emprunteurs trouvés: " . count($formationBorrowersWithDetails) . "</p>";
    if (count($formationBorrowersWithDetails) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers emprunteurs seulement pour éviter une sortie trop longue
        $displayBorrowers = array_slice($formationBorrowersWithDetails, 0, 5);
        print_r($displayBorrowers);
        if (count($formationBorrowersWithDetails) > 5) {
            echo "... (et " . (count($formationBorrowersWithDetails) - 5) . " autres emprunteurs)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun emprunteur trouvé pour cette formation.</p>";
    }
} else {
    echo "<p>Aucune formation trouvée dans la base de données.</p>";
}

// Test 6: Récupération des statistiques de prêts par formation
echo "<h2>Test 6: Récupération des statistiques de prêts par formation</h2>";
echo "<p>Cette fonction récupère des statistiques sur les prêts (total, actifs, en retard) pour chaque formation.</p>";

// Exécution de la méthode getLoanStatsByFormation() qui retourne les statistiques de prêts par formation
$loanStatsByFormation = $formationModel->getLoanStatsByFormation();

// Affichage des résultats
echo "<h3>Statistiques de prêts par formation:</h3>";
echo "<p>Nombre de formations avec statistiques: " . count($loanStatsByFormation) . "</p>";
if (count($loanStatsByFormation) > 0) {
    echo "<pre>";
    print_r($loanStatsByFormation);
    echo "</pre>";
} else {
    echo "<p>Aucune statistique trouvée.</p>";
}

// Test 7: Création d'une nouvelle formation (commenté pour éviter de modifier la base de données)
echo "<h2>Test 7: Création d'une nouvelle formation</h2>";
echo "<p>Cette fonction crée une nouvelle formation dans la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la création d'une formation.</p>";

/*
// Données pour la nouvelle formation
$formationData = [
    'formation' => 'Test Formation ' . time()
];

// Exécution de la méthode create() pour créer une nouvelle formation
$newFormationId = $formationModel->create($formationData);

// Affichage des résultats
echo "<h3>Résultats de la création:</h3>";
if ($newFormationId) {
    echo "<p>Nouvelle formation créée avec succès. ID: $newFormationId</p>";
    
    // Récupération de la nouvelle formation pour vérification
    $newFormation = $formationModel->getById($newFormationId);
    echo "<pre>";
    print_r($newFormation);
    echo "</pre>";
} else {
    echo "<p>Échec de la création de la formation.</p>";
}
*/

// Lien pour revenir à la page d'accueil des tests
echo "<p><a href='index.php'>Retour à la page d'accueil des tests</a></p>";