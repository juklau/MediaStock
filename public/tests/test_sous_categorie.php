<?php
/**
 * Test du modèle SousCategorie
 * 
 * Ce script teste les fonctionnalités du modèle SousCategorie, notamment:
 * - Récupération de toutes les sous-catégories
 * - Récupération d'une sous-catégorie par ID
 * - Récupération des sous-catégories avec leurs informations de catégorie parente
 * - Récupération des sous-catégories par catégorie
 * - Récupération des items d'une sous-catégorie
 * - Création d'une nouvelle sous-catégorie
 * - Mise à jour de la catégorie d'une sous-catégorie
 */

// Inclusion de l'autoloader pour charger automatiquement les classes
require_once '../autoload.php';

// Titre de la page
echo "<h1>Test du modèle SousCategorie</h1>";

// Création d'une instance du modèle SousCategorie
$sousCategorieModel = new Models\SousCategorie();

// Test 1: Récupération de toutes les sous-catégories
echo "<h2>Test 1: Récupération de toutes les sous-catégories</h2>";
echo "<p>Cette fonction récupère toutes les sous-catégories de la base de données.</p>";

// Exécution de la méthode getAll() qui retourne toutes les sous-catégories
$sousCategories = $sousCategorieModel->getAll();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de sous-catégories trouvées: " . count($sousCategories) . "</p>";
echo "<pre>";
print_r($sousCategories);
echo "</pre>";

// Test 2: Récupération d'une sous-catégorie par ID
echo "<h2>Test 2: Récupération d'une sous-catégorie par ID</h2>";
echo "<p>Cette fonction récupère une sous-catégorie spécifique par son ID.</p>";

// Vérification qu'il y a au moins une sous-catégorie dans la base de données
if (count($sousCategories) > 0) {
    // Récupération du premier ID de sous-catégorie
    $sousCategorieId = $sousCategories[0]['id'];
    
    // Exécution de la méthode getById() pour récupérer la sous-catégorie
    $sousCategorie = $sousCategorieModel->getById($sousCategorieId);
    
    // Affichage des résultats
    echo "<h3>Résultats pour la sous-catégorie avec ID $sousCategorieId:</h3>";
    echo "<pre>";
    print_r($sousCategorie);
    echo "</pre>";
} else {
    echo "<p>Aucune sous-catégorie trouvée dans la base de données.</p>";
}

// Test 3: Récupération des sous-catégories avec leurs informations de catégorie parente
echo "<h2>Test 3: Récupération des sous-catégories avec leurs informations de catégorie parente</h2>";
echo "<p>Cette fonction récupère toutes les sous-catégories avec les informations de leur catégorie parente.</p>";

// Exécution de la méthode getAllWithCategory() qui retourne toutes les sous-catégories avec leur catégorie
$sousCategoriesWithCategory = $sousCategorieModel->getAllWithCategory();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de sous-catégories trouvées: " . count($sousCategoriesWithCategory) . "</p>";
echo "<pre>";
print_r($sousCategoriesWithCategory);
echo "</pre>";

// Test 4: Récupération d'une sous-catégorie avec ses informations de catégorie parente
echo "<h2>Test 4: Récupération d'une sous-catégorie avec ses informations de catégorie parente</h2>";
echo "<p>Cette fonction récupère une sous-catégorie spécifique avec les informations de sa catégorie parente.</p>";

// Vérification qu'il y a au moins une sous-catégorie dans la base de données
if (count($sousCategories) > 0) {
    // Récupération du premier ID de sous-catégorie
    $sousCategorieId = $sousCategories[0]['id'];
    
    // Exécution de la méthode getWithCategory() pour récupérer la sous-catégorie avec sa catégorie
    $sousCategorieWithCategory = $sousCategorieModel->getWithCategory($sousCategorieId);
    
    // Affichage des résultats
    echo "<h3>Résultats pour la sous-catégorie avec ID $sousCategorieId:</h3>";
    echo "<pre>";
    print_r($sousCategorieWithCategory);
    echo "</pre>";
} else {
    echo "<p>Aucune sous-catégorie trouvée dans la base de données.</p>";
}

// Test 5: Récupération des sous-catégories par catégorie
echo "<h2>Test 5: Récupération des sous-catégories par catégorie</h2>";
echo "<p>Cette fonction récupère toutes les sous-catégories appartenant à une catégorie spécifique.</p>";

// Récupération d'une instance du modèle Categorie pour obtenir une catégorie existante
$categorieModel = new Models\Categorie();
$categories = $categorieModel->getAll();

// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categorieId = $categories[0]['id'];
    
    // Exécution de la méthode getByCategory() pour récupérer les sous-catégories de cette catégorie
    $sousCategoriesByCategory = $sousCategorieModel->getByCategory($categorieId);
    
    // Affichage des résultats
    echo "<h3>Sous-catégories de la catégorie avec ID $categorieId:</h3>";
    echo "<p>Nombre de sous-catégories trouvées: " . count($sousCategoriesByCategory) . "</p>";
    if (count($sousCategoriesByCategory) > 0) {
        echo "<pre>";
        print_r($sousCategoriesByCategory);
        echo "</pre>";
    } else {
        echo "<p>Aucune sous-catégorie trouvée pour cette catégorie.</p>";
    }
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données.</p>";
}

// Test 6: Récupération des items d'une sous-catégorie
echo "<h2>Test 6: Récupération des items d'une sous-catégorie</h2>";
echo "<p>Cette fonction récupère tous les items appartenant à une sous-catégorie spécifique.</p>";

// Vérification qu'il y a au moins une sous-catégorie dans la base de données
if (count($sousCategories) > 0) {
    // Récupération du premier ID de sous-catégorie
    $sousCategorieId = $sousCategories[0]['id'];
    
    // Exécution de la méthode getSubcategoryItems() pour récupérer les items de cette sous-catégorie
    $subcategoryItems = $sousCategorieModel->getSubcategoryItems($sousCategorieId);
    
    // Affichage des résultats
    echo "<h3>Items de la sous-catégorie avec ID $sousCategorieId:</h3>";
    echo "<p>Nombre d'items trouvés: " . count($subcategoryItems) . "</p>";
    if (count($subcategoryItems) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers items seulement pour éviter une sortie trop longue
        $displayItems = array_slice($subcategoryItems, 0, 5);
        print_r($displayItems);
        if (count($subcategoryItems) > 5) {
            echo "... (et " . (count($subcategoryItems) - 5) . " autres items)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun item trouvé pour cette sous-catégorie.</p>";
    }
} else {
    echo "<p>Aucune sous-catégorie trouvée dans la base de données.</p>";
}

// Test 7: Création d'une nouvelle sous-catégorie (commenté pour éviter de modifier la base de données)
echo "<h2>Test 7: Création d'une nouvelle sous-catégorie</h2>";
echo "<p>Cette fonction crée une nouvelle sous-catégorie dans la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la création d'une sous-catégorie.</p>";

/*
// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categorieId = $categories[0]['id'];
    
    // Exécution de la méthode createSubcategory() pour créer une nouvelle sous-catégorie
    $newSubcategoryName = 'Test Sous-catégorie ' . time();
    $newSubcategoryId = $sousCategorieModel->createSubcategory($newSubcategoryName, $categorieId);
    
    // Affichage des résultats
    echo "<h3>Résultats de la création:</h3>";
    if ($newSubcategoryId) {
        echo "<p>Nouvelle sous-catégorie créée avec succès. ID: $newSubcategoryId</p>";
        
        // Récupération de la nouvelle sous-catégorie pour vérification
        $newSubcategory = $sousCategorieModel->getById($newSubcategoryId);
        echo "<pre>";
        print_r($newSubcategory);
        echo "</pre>";
    } else {
        echo "<p>Échec de la création de la sous-catégorie.</p>";
    }
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données pour associer à la sous-catégorie.</p>";
}
*/

// Test 8: Mise à jour de la catégorie d'une sous-catégorie (commenté pour éviter de modifier la base de données)
echo "<h2>Test 8: Mise à jour de la catégorie d'une sous-catégorie</h2>";
echo "<p>Cette fonction met à jour la catégorie parente d'une sous-catégorie existante.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la mise à jour d'une sous-catégorie.</p>";

/*
// Vérification qu'il y a au moins une sous-catégorie et au moins deux catégories dans la base de données
if (count($sousCategories) > 0 && count($categories) > 1) {
    // Récupération du premier ID de sous-catégorie
    $sousCategorieId = $sousCategories[0]['id'];
    
    // Récupération du deuxième ID de catégorie (différent de la catégorie actuelle de la sous-catégorie)
    $currentCategoryId = $sousCategories[0]['categorie_id'];
    $newCategoryId = null;
    
    foreach ($categories as $category) {
        if ($category['id'] != $currentCategoryId) {
            $newCategoryId = $category['id'];
            break;
        }
    }
    
    if ($newCategoryId) {
        // Exécution de la méthode updateCategory() pour mettre à jour la catégorie de la sous-catégorie
        $updateResult = $sousCategorieModel->updateCategory($sousCategorieId, $newCategoryId);
        
        // Affichage des résultats
        echo "<h3>Résultats de la mise à jour:</h3>";
        if ($updateResult) {
            echo "<p>Catégorie de la sous-catégorie mise à jour avec succès.</p>";
            
            // Récupération de la sous-catégorie mise à jour pour vérification
            $updatedSubcategory = $sousCategorieModel->getWithCategory($sousCategorieId);
            echo "<pre>";
            print_r($updatedSubcategory);
            echo "</pre>";
        } else {
            echo "<p>Échec de la mise à jour de la catégorie de la sous-catégorie.</p>";
        }
    } else {
        echo "<p>Impossible de trouver une catégorie différente pour la mise à jour.</p>";
    }
} else {
    echo "<p>Pas assez de sous-catégories ou de catégories dans la base de données pour effectuer ce test.</p>";
}
*/

// Lien pour revenir à la page d'accueil des tests
echo "<p><a href='index.php'>Retour à la page d'accueil des tests</a></p>";