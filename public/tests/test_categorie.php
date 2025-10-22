<?php
/**
 * Test du modèle Categorie
 * 
 * Ce script teste les fonctionnalités du modèle Categorie, notamment:
 * - Récupération de toutes les catégories
 * - Récupération d'une catégorie par ID
 * - Récupération des catégories avec leurs sous-catégories
 * - Récupération des items d'une catégorie
 * - Récupération de tous les items d'une catégorie et de ses sous-catégories
 * - Création d'une nouvelle catégorie avec sous-catégories
 * - Suppression d'une catégorie et de ses sous-catégories
 */

// Inclusion de l'autoloader pour charger automatiquement les classes
require_once '../autoload.php';

// Titre de la page
echo "<h1>Test du modèle Categorie</h1>";

// Création d'une instance du modèle Categorie
$categorieModel = new Models\Categorie();

// Test 1: Récupération de toutes les catégories
echo "<h2>Test 1: Récupération de toutes les catégories</h2>";
echo "<p>Cette fonction récupère toutes les catégories de la base de données.</p>";

// Exécution de la méthode getAll() qui retourne toutes les catégories
$categories = $categorieModel->getAll();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de catégories trouvées: " . count($categories) . "</p>";
echo "<pre>";
print_r($categories);
echo "</pre>";

// Test 2: Récupération d'une catégorie par ID
echo "<h2>Test 2: Récupération d'une catégorie par ID</h2>";
echo "<p>Cette fonction récupère une catégorie spécifique par son ID.</p>";

// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categorieId = $categories[0]['id'];
    
    // Exécution de la méthode getById() pour récupérer la catégorie
    $categorie = $categorieModel->getById($categorieId);
    
    // Affichage des résultats
    echo "<h3>Résultats pour la catégorie avec ID $categorieId:</h3>";
    echo "<pre>";
    print_r($categorie);
    echo "</pre>";
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données.</p>";
}

// Test 3: Récupération des catégories avec leurs sous-catégories
echo "<h2>Test 3: Récupération des catégories avec leurs sous-catégories</h2>";
echo "<p>Cette fonction récupère toutes les catégories avec leurs sous-catégories associées.</p>";

// Exécution de la méthode getAllWithSubcategories() qui retourne toutes les catégories avec leurs sous-catégories
$categoriesWithSubcategories = $categorieModel->getAllWithSubcategories();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de catégories trouvées: " . count($categoriesWithSubcategories) . "</p>";
echo "<pre>";
print_r($categoriesWithSubcategories);
echo "</pre>";

// Test 4: Récupération d'une catégorie avec ses sous-catégories
echo "<h2>Test 4: Récupération d'une catégorie avec ses sous-catégories</h2>";
echo "<p>Cette fonction récupère une catégorie spécifique avec ses sous-catégories associées.</p>";

// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categorieId = $categories[0]['id'];
    
    // Exécution de la méthode getWithSubcategories() pour récupérer la catégorie avec ses sous-catégories
    $categorieWithSubcategories = $categorieModel->getWithSubcategories($categorieId);
    
    // Affichage des résultats
    echo "<h3>Résultats pour la catégorie avec ID $categorieId:</h3>";
    echo "<pre>";
    print_r($categorieWithSubcategories);
    echo "</pre>";
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données.</p>";
}

// Test 5: Récupération des items d'une catégorie
echo "<h2>Test 5: Récupération des items d'une catégorie</h2>";
echo "<p>Cette fonction récupère tous les items appartenant à une catégorie spécifique.</p>";

// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categorieId = $categories[0]['id'];
    
    // Exécution de la méthode getCategoryItems() pour récupérer les items de cette catégorie
    $categoryItems = $categorieModel->getCategoryItems($categorieId);
    
    // Affichage des résultats
    echo "<h3>Items de la catégorie avec ID $categorieId:</h3>";
    echo "<p>Nombre d'items trouvés: " . count($categoryItems) . "</p>";
    if (count($categoryItems) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers items seulement pour éviter une sortie trop longue
        $displayItems = array_slice($categoryItems, 0, 5);
        print_r($displayItems);
        if (count($categoryItems) > 5) {
            echo "... (et " . (count($categoryItems) - 5) . " autres items)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun item trouvé pour cette catégorie.</p>";
    }
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données.</p>";
}

// Test 6: Récupération de tous les items d'une catégorie et de ses sous-catégories
echo "<h2>Test 6: Récupération de tous les items d'une catégorie et de ses sous-catégories</h2>";
echo "<p>Cette fonction récupère tous les items appartenant à une catégorie et à ses sous-catégories.</p>";

// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categorieId = $categories[0]['id'];
    
    // Exécution de la méthode getAllCategoryItems() pour récupérer tous les items de cette catégorie et de ses sous-catégories
    $allCategoryItems = $categorieModel->getAllCategoryItems($categorieId);
    
    // Affichage des résultats
    echo "<h3>Tous les items de la catégorie avec ID $categorieId et de ses sous-catégories:</h3>";
    echo "<p>Nombre d'items trouvés: " . count($allCategoryItems) . "</p>";
    if (count($allCategoryItems) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers items seulement pour éviter une sortie trop longue
        $displayItems = array_slice($allCategoryItems, 0, 5);
        print_r($displayItems);
        if (count($allCategoryItems) > 5) {
            echo "... (et " . (count($allCategoryItems) - 5) . " autres items)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun item trouvé pour cette catégorie et ses sous-catégories.</p>";
    }
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données.</p>";
}

// Test 7: Création d'une nouvelle catégorie avec sous-catégories (commenté pour éviter de modifier la base de données)
echo "<h2>Test 7: Création d'une nouvelle catégorie avec sous-catégories</h2>";
echo "<p>Cette fonction crée une nouvelle catégorie avec des sous-catégories dans la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la création d'une catégorie.</p>";

/*
// Données pour la nouvelle catégorie
$categoryName = 'Test Catégorie ' . time();
$subcategories = ['Test Sous-catégorie 1', 'Test Sous-catégorie 2'];

// Exécution de la méthode createWithSubcategories() pour créer une nouvelle catégorie avec sous-catégories
$newCategoryId = $categorieModel->createWithSubcategories($categoryName, $subcategories);

// Affichage des résultats
echo "<h3>Résultats de la création:</h3>";
if ($newCategoryId) {
    echo "<p>Nouvelle catégorie créée avec succès. ID: $newCategoryId</p>";
    
    // Récupération de la nouvelle catégorie avec ses sous-catégories pour vérification
    $newCategory = $categorieModel->getWithSubcategories($newCategoryId);
    echo "<pre>";
    print_r($newCategory);
    echo "</pre>";
} else {
    echo "<p>Échec de la création de la catégorie.</p>";
}
*/

// Test 8: Suppression d'une catégorie et de ses sous-catégories (commenté pour éviter de modifier la base de données)
echo "<h2>Test 8: Suppression d'une catégorie et de ses sous-catégories</h2>";
echo "<p>Cette fonction supprime une catégorie et toutes ses sous-catégories de la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la suppression d'une catégorie.</p>";
echo "<p><strong>Attention:</strong> La suppression d'une catégorie peut échouer si des items y sont associés en raison des contraintes de clé étrangère.</p>";

/*
// Pour ce test, nous utiliserions normalement une catégorie créée spécifiquement pour le test
// Mais comme nous avons commenté la création, nous allons simplement montrer le code

// ID de la catégorie à supprimer (à remplacer par un ID valide si vous décommentez ce code)
$categoryIdToDelete = 999; // ID fictif

// Exécution de la méthode deleteWithSubcategories() pour supprimer la catégorie et ses sous-catégories
$deleteResult = $categorieModel->deleteWithSubcategories($categoryIdToDelete);

// Affichage des résultats
echo "<h3>Résultats de la suppression:</h3>";
if ($deleteResult) {
    echo "<p>Catégorie et ses sous-catégories supprimées avec succès.</p>";
} else {
    echo "<p>Échec de la suppression de la catégorie. Vérifiez qu'aucun item n'y est associé.</p>";
}
*/

// Lien pour revenir à la page d'accueil des tests
echo "<p><a href='index.php'>Retour à la page d'accueil des tests</a></p>";