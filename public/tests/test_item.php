<?php
/**
 * Test du modèle Item
 * 
 * Ce script teste les fonctionnalités du modèle Item, notamment:
 * - Récupération de tous les items
 * - Récupération d'un item par ID
 * - Récupération des items avec leurs informations de catégorie
 * - Recherche d'items par nom
 * - Récupération des items par catégorie
 * - Récupération des items par état
 * - Récupération des items disponibles (non empruntés)
 * - Recherche d'un item par QR code
 * - Vérification de la disponibilité d'un item pour un prêt
 */

// Inclusion de l'autoloader pour charger automatiquement les classes
require_once '../autoload.php';

// Titre de la page
echo "<h1>Test du modèle Item</h1>";

// Création d'une instance du modèle Item
$itemModel = new Models\Item();

// Test 1: Récupération de tous les items
echo "<h2>Test 1: Récupération de tous les items</h2>";
echo "<p>Cette fonction récupère tous les items de la base de données.</p>";

// Exécution de la méthode getAll() qui retourne tous les items
$items = $itemModel->getAll();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre d'items trouvés: " . count($items) . "</p>";
echo "<pre>";
// Affichage des 5 premiers items seulement pour éviter une sortie trop longue
$displayItems = array_slice($items, 0, 5);
print_r($displayItems);
if (count($items) > 5) {
    echo "... (et " . (count($items) - 5) . " autres items)";
}
echo "</pre>";

// Test 2: Récupération d'un item par ID
echo "<h2>Test 2: Récupération d'un item par ID</h2>";
echo "<p>Cette fonction récupère un item spécifique par son ID.</p>";

// Vérification qu'il y a au moins un item dans la base de données
if (count($items) > 0) {
    // Récupération du premier ID d'item
    $itemId = $items[0]['id'];

    // Exécution de la méthode getById() pour récupérer l'item
    $item = $itemModel->getById($itemId);

    // Affichage des résultats
    echo "<h3>Résultats pour l'item avec ID $itemId:</h3>";
    echo "<pre>";
    print_r($item);
    echo "</pre>";
} else {
    echo "<p>Aucun item trouvé dans la base de données.</p>";
}

// Test 3: Récupération des items avec leurs informations de catégorie
echo "<h2>Test 3: Récupération des items avec leurs informations de catégorie</h2>";
echo "<p>Cette fonction récupère tous les items avec les informations de leur catégorie.</p>";

// Exécution de la méthode getAllWithCategory() qui retourne tous les items avec leur catégorie
$itemsWithCategory = $itemModel->getAllWithCategory();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre d'items trouvés: " . count($itemsWithCategory) . "</p>";
echo "<pre>";
// Affichage des 5 premiers items seulement pour éviter une sortie trop longue
$displayItems = array_slice($itemsWithCategory, 0, 5);
print_r($displayItems);
if (count($itemsWithCategory) > 5) {
    echo "... (et " . (count($itemsWithCategory) - 5) . " autres items)";
}
echo "</pre>";

// Test 4: Recherche d'items par nom
echo "<h2>Test 4: Recherche d'items par nom</h2>";
echo "<p>Cette fonction recherche des items dont le nom contient un terme spécifique.</p>";

// Terme de recherche (à adapter selon votre base de données)
// Utilisation d'un terme générique qui a des chances de trouver des résultats
$searchTerm = 'a';

// Exécution de la méthode searchByName() pour rechercher des items
$searchResults = $itemModel->searchByName($searchTerm);

// Affichage des résultats
echo "<h3>Résultats de la recherche pour le terme '$searchTerm':</h3>";
echo "<p>Nombre d'items trouvés: " . count($searchResults) . "</p>";
if (count($searchResults) > 0) {
    echo "<pre>";
    // Affichage des 5 premiers résultats seulement pour éviter une sortie trop longue
    $displayResults = array_slice($searchResults, 0, 5);
    print_r($displayResults);
    if (count($searchResults) > 5) {
        echo "... (et " . (count($searchResults) - 5) . " autres items)";
    }
    echo "</pre>";
} else {
    echo "<p>Aucun item trouvé pour ce terme de recherche.</p>";
}

// Test 5: Récupération des items par catégorie
echo "<h2>Test 5: Récupération des items par catégorie</h2>";
echo "<p>Cette fonction récupère tous les items appartenant à une catégorie spécifique.</p>";

// Récupération d'une instance du modèle Categorie pour obtenir une catégorie existante
$categorieModel = new Models\Categorie();
$categories = $categorieModel->getAll();

// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categoryId = $categories[0]['id'];

    // Exécution de la méthode getByCategory() pour récupérer les items de cette catégorie
    $itemsByCategory = $itemModel->getByCategory($categoryId);

    // Affichage des résultats
    echo "<h3>Items de la catégorie avec ID $categoryId:</h3>";
    echo "<p>Nombre d'items trouvés: " . count($itemsByCategory) . "</p>";
    if (count($itemsByCategory) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers items seulement pour éviter une sortie trop longue
        $displayItems = array_slice($itemsByCategory, 0, 5);
        print_r($displayItems);
        if (count($itemsByCategory) > 5) {
            echo "... (et " . (count($itemsByCategory) - 5) . " autres items)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun item trouvé pour cette catégorie.</p>";
    }
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données.</p>";
}

// Test 6: Récupération des items par état
echo "<h2>Test 6: Récupération des items par état</h2>";
echo "<p>Cette fonction récupère tous les items ayant un état spécifique (bon, moyen, mauvais).</p>";

// État à rechercher
$condition = 'bon';

// Exécution de la méthode getByCondition() pour récupérer les items avec cet état
$itemsByCondition = $itemModel->getByCondition($condition);

// Affichage des résultats
echo "<h3>Items en état '$condition':</h3>";
echo "<p>Nombre d'items trouvés: " . count($itemsByCondition) . "</p>";
if (count($itemsByCondition) > 0) {
    echo "<pre>";
    // Affichage des 5 premiers items seulement pour éviter une sortie trop longue
    $displayItems = array_slice($itemsByCondition, 0, 5);
    print_r($displayItems);
    if (count($itemsByCondition) > 5) {
        echo "... (et " . (count($itemsByCondition) - 5) . " autres items)";
    }
    echo "</pre>";
} else {
    echo "<p>Aucun item trouvé avec cet état.</p>";
}

// Test 7: Récupération des items disponibles
echo "<h2>Test 7: Récupération des items disponibles</h2>";
echo "<p>Cette fonction récupère tous les items qui ne sont pas actuellement empruntés.</p>";

// Exécution de la méthode getAvailableItems() pour récupérer les items disponibles
$availableItems = $itemModel->getAvailableItems();

// Affichage des résultats
echo "<h3>Items disponibles:</h3>";
echo "<p>Nombre d'items disponibles: " . count($availableItems) . "</p>";
if (count($availableItems) > 0) {
    echo "<pre>";
    // Affichage des 5 premiers items seulement pour éviter une sortie trop longue
    $displayItems = array_slice($availableItems, 0, 5);
    print_r($displayItems);
    if (count($availableItems) > 5) {
        echo "... (et " . (count($availableItems) - 5) . " autres items)";
    }
    echo "</pre>";
} else {
    echo "<p>Aucun item disponible trouvé.</p>";
}

// Test 8: Recherche d'un item par QR code
echo "<h2>Test 8: Recherche d'un item par QR code</h2>";
echo "<p>Cette fonction recherche un item spécifique par son QR code.</p>";

// QR code à rechercher (à adapter selon votre base de données)
// Si nous avons des items, utilisons le QR code du premier item
if (count($items) > 0 && isset($items[0]['qr_code'])) {
    $qrCode = $items[0]['qr_code'];

    // Exécution de la méthode findByQrCode() pour rechercher l'item
    $itemByQrCode = $itemModel->findByQrCode($qrCode);

    // Affichage des résultats
    echo "<h3>Résultats de la recherche pour le QR code '$qrCode':</h3>";
    if ($itemByQrCode) {
        echo "<pre>";
        print_r($itemByQrCode);
        echo "</pre>";
    } else {
        echo "<p>Aucun item trouvé avec ce QR code.</p>";
    }
} else {
    echo "<p>Aucun QR code disponible pour effectuer le test.</p>";
}

// Test 9: Création d'un nouvel item (commenté pour éviter de modifier la base de données)
echo "<h2>Test 9: Création d'un nouvel item</h2>";
echo "<p>Cette fonction crée un nouvel item dans la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la création d'un item.</p>";

/*
// Vérification qu'il y a au moins une catégorie dans la base de données
if (count($categories) > 0) {
    // Récupération du premier ID de catégorie
    $categoryId = $categories[0]['id'];

    // Données pour le nouvel item
    $newItemData = [
        'nom' => 'Test Item ' . time(),
        'model' => 'Test Model',
        'qr_code' => 'QR' . time(),
        'image_url' => 'images/test.jpg',
        'etat' => 'bon',
        'categorie_id' => $categoryId
    ];

    // Exécution de la méthode create() pour créer un nouvel item
    $newItemId = $itemModel->create($newItemData);

    // Affichage des résultats
    echo "<h3>Résultats de la création:</h3>";
    if ($newItemId) {
        echo "<p>Nouvel item créé avec succès. ID: $newItemId</p>";

        // Récupération du nouvel item pour vérification
        $newItem = $itemModel->getById($newItemId);
        echo "<pre>";
        print_r($newItem);
        echo "</pre>";
    } else {
        echo "<p>Échec de la création de l'item.</p>";
    }
} else {
    echo "<p>Aucune catégorie trouvée dans la base de données pour associer à l'item.</p>";
}
*/

// Test 10: Vérification de la disponibilité d'un item
echo "<h2>Test 10: Vérification de la disponibilité d'un item</h2>";
echo "<p>Cette fonction vérifie si un item est disponible pour un prêt (aucun prêt en cours).</p>";

// Vérification qu'il y a au moins un item dans la base de données
if (count($items) > 0) {
    // Récupération du premier ID d'item
    $itemId = $items[0]['id'];

    // Exécution de la méthode isAvailable() pour vérifier la disponibilité de l'item
    $isAvailable = $itemModel->isAvailable($itemId);

    // Affichage des résultats
    echo "<h3>Résultats pour l'item avec ID $itemId:</h3>";
    if ($isAvailable) {
        echo "<p>L'item est <strong>disponible</strong> pour un prêt.</p>";
    } else {
        echo "<p>L'item est <strong>indisponible</strong> (déjà emprunté).</p>";

        // Récupération du prêt actif pour cet item
        $pretModel = new Models\Pret();
        $currentLoan = $pretModel->getCurrentItemLoan($itemId);

        if ($currentLoan) {
            echo "<p>Détails du prêt en cours:</p>";
            echo "<pre>";
            print_r($currentLoan);
            echo "</pre>";
        }
    }

    // Vérification avec un autre item (si disponible)
    if (count($availableItems) > 0) {
        $availableItemId = $availableItems[0]['id'];
        $isDefinitelyAvailable = $itemModel->isAvailable($availableItemId);

        echo "<h3>Vérification avec un item connu comme disponible (ID: $availableItemId):</h3>";
        if ($isDefinitelyAvailable) {
            echo "<p>L'item est bien <strong>disponible</strong> pour un prêt, comme attendu.</p>";
        } else {
            echo "<p>Erreur: L'item devrait être disponible mais est marqué comme indisponible.</p>";
        }
    }
} else {
    echo "<p>Aucun item trouvé dans la base de données.</p>";
}

// Lien pour revenir à la page d'accueil des tests
echo "<p><a href='index.php'>Retour à la page d'accueil des tests</a></p>";
