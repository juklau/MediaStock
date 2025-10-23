<?php
 require_once __DIR__ . '/autoload.php';
    // Fonction de chargement automatique => 
    // spl_autoload_register(function ($class) {
    //     $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    //     if (file_exists($file)) {
    //         require $file;
    //         return true;
    //     }
    //     return false;
    // });

     $prefixes = [
        // __DIR__ = le dossier du fichier actuel!!
        // en PHP '\\' === '\' dans une chaîne!!
        // Si une classe commence par le namespace Models\, alors cherche son fichier dans src/models/
        'Models\\' => __DIR__ . '/../src/models/',
        // 'Config\\' => __DIR__ . '/../config/',
        // 'Controllers\\' => __DIR__ . '/../src/controllers/',
        // 'Controllers\\' => __DIR__ . '/../src/views/',
    ];

    // Exemple d'utilisation de la couche d'accès aux données
    try {
        // La connexion à la base de données est gérée par le modèle singleton
        
        // Exemple : obtenir tous les éléments avec leurs informations de catégorie
        $itemModel = new Models\Item();
        $items = $itemModel->getAllWithCategory();
        
        echo "<h1>Démonstration de la couche d'accès aux données MediaStock</h1>";
        
        // Display items
        echo "<h2>Articles avec catégories</h2>";
        echo "<pre>";
        print_r($items);
        echo "</pre>";
        
        // Exemple : Obtenir tous les prêts actifs
        $pretModel = new Models\Pret();
        $activeLoans = $pretModel->getActiveLoans();
        
        echo "<h2>Prêts actifs</h2>";
        echo "<pre>";
        print_r($activeLoans);
        echo "</pre>";
        
        // Example: Get all categories with subcategories
        $categorieModel = new Models\Categorie();
        $categories = $categorieModel->getAllWithSubcategories();
        
        echo "<h2>Catégories avec sous-catégories</h2>";
        echo "<pre>";
        print_r($categories);
        echo "</pre>";
        
        // Example: Get all formations with borrower count
        $formationModel = new Models\Formation();
        $formations = $formationModel->getAllWithBorrowerCount();
        
        echo "<h2>Formations avec nombre d'emprunteurs</h2>";
        echo "<pre>";
        print_r($formations);
        echo "</pre>";
        
        // Exemple : Obtenir tous les emprunteurs avec leurs informations de formation
        $emprunteurModel = new Models\Emprunteur();
        $borrowers = $emprunteurModel->getAllWithFormation();
        
        echo "<h2>Emprunteurs avec formation</h2>";
        echo "<pre>";
        print_r($borrowers);
        echo "</pre>";
        
    } catch (Exception $e) {
        echo "<h1>Erreur</h1>";
        echo "<p>Une erreur s'est produite: " . $e->getMessage() . "</p>";
    }

    // Documentation
    echo "<h1>Data Access Layer Documentation</h1>";
    echo "<p>This data access layer provides a clean and well-encapsulated interface to the MediaStock database.</p>";

    echo "<h2>Available Models</h2>";
    echo "<ul>";
    echo "<li><strong>Item</strong> - Manages items in the inventory</li>";
    echo "<li><strong>Pret</strong> - Manages loans of items to borrowers</li>";
    echo "<li><strong>Administrateur</strong> - Manages administrators</li>";
    echo "<li><strong>Emprunteur</strong> - Manages borrowers</li>";
    echo "<li><strong>Categorie</strong> - Manages categories</li>";
    echo "<li><strong>Formation</strong> - Manages formations</li>";
    echo "<li><strong>SousCategorie</strong> - Manages subcategories</li>";
    echo "</ul>";

    echo "<h2>Common Operations</h2>";
    echo "<h3>Basic CRUD Operations (available on all models)</h3>";
    echo "<ul>";
    echo "<li><code>getAll()</code> - Get all records</li>";
    echo "<li><code>getById(\$id)</code> - Get a record by ID</li>";
    echo "<li><code>create(\$data)</code> - Create a new record</li>";
    echo "<li><code>update(\$id, \$data)</code> - Update a record</li>";
    echo "<li><code>delete(\$id)</code> - Delete a record</li>";
    echo "<li><code>findBy(\$field, \$value)</code> - Find records by a specific field value</li>";
    echo "</ul>";

    echo "<h3>Item Operations</h3>";
    echo "<ul>";
    echo "<li><code>getAllWithCategory()</code> - Get all items with their category information</li>";
    echo "<li><code>getWithCategory(\$id)</code> - Get an item with its category information</li>";
    echo "<li><code>getByCategory(\$categoryId)</code> - Get items by category</li>";
    echo "<li><code>getByCondition(\$condition)</code> - Get items by condition (état)</li>";
    echo "<li><code>findByQrCode(\$qrCode)</code> - Find an item by QR code</li>";
    echo "<li><code>searchByName(\$searchTerm)</code> - Search items by name</li>";
    echo "<li><code>getAvailableItems()</code> - Get available items (not currently on loan)</li>";
    echo "</ul>";

    echo "<h3>Loan Operations</h3>";
    echo "<ul>";
    echo "<li><code>getAllWithDetails()</code> - Get all loans with related information</li>";
    echo "<li><code>getWithDetails(\$id)</code> - Get a specific loan with related information</li>";
    echo "<li><code>getActiveLoans()</code> - Get active loans (not yet returned)</li>";
    echo "<li><code>getOverdueLoans()</code> - Get overdue loans</li>";
    echo "<li><code>getLoansByBorrower(\$emprunteurId)</code> - Get loans by borrower</li>";
    echo "<li><code>getLoansByItem(\$itemId)</code> - Get loans by item</li>";
    echo "<li><code>endLoan(\$id, \$returnDate, \$finalNote)</code> - End a loan by setting the effective return date</li>";
    echo "<li><code>createLoan(\$itemId, \$emprunteurId, \$preteurId, \$dateSortie, \$dateRetourPrevue, \$noteDebut)</code> - Create a new loan</li>";
    echo "</ul>";

    echo "<p>For more details, please refer to the documentation in each model class.</p>";