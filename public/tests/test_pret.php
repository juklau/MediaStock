<?php
/**
 * Test du modèle Pret (Loan)
 * 
 * Ce script teste les fonctionnalités du modèle Pret, notamment:
 * - Récupération de tous les prêts
 * - Récupération d'un prêt par ID
 * - Récupération des prêts avec détails (item, emprunteur, prêteur)
 * - Récupération des prêts actifs (non retournés)
 * - Récupération des prêts en retard
 * - Récupération des prêts par emprunteur
 * - Récupération des prêts par item
 * - Création d'un nouveau prêt
 * - Fin d'un prêt (retour d'item)
 */

// Inclusion de l'autoloader pour charger automatiquement les classes
require_once '../autoload.php';

// Titre de la page
echo "<h1>Test du modèle Pret (Loan)</h1>";

// Création d'une instance du modèle Pret
$pretModel = new Models\Pret();

// Test 1: Récupération de tous les prêts
echo "<h2>Test 1: Récupération de tous les prêts</h2>";
echo "<p>Cette fonction récupère tous les prêts de la base de données.</p>";

// Exécution de la méthode getAll() qui retourne tous les prêts
$prets = $pretModel->getAll();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de prêts trouvés: " . count($prets) . "</p>";
echo "<pre>";
// Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
$displayPrets = array_slice($prets, 0, 5);
print_r($displayPrets);
if (count($prets) > 5) {
    echo "... (et " . (count($prets) - 5) . " autres prêts)";
}
echo "</pre>";

// Test 2: Récupération d'un prêt par ID
echo "<h2>Test 2: Récupération d'un prêt par ID</h2>";
echo "<p>Cette fonction récupère un prêt spécifique par son ID.</p>";

// Vérification qu'il y a au moins un prêt dans la base de données
if (count($prets) > 0) {
    // Récupération du premier ID de prêt
    $pretId = $prets[0]['id'];

    // Exécution de la méthode getById() pour récupérer le prêt
    $pret = $pretModel->getById($pretId);

    // Affichage des résultats
    echo "<h3>Résultats pour le prêt avec ID $pretId:</h3>";
    echo "<pre>";
    print_r($pret);
    echo "</pre>";
} else {
    echo "<p>Aucun prêt trouvé dans la base de données.</p>";
}

// Test 3: Récupération des prêts avec détails
echo "<h2>Test 3: Récupération des prêts avec détails</h2>";
echo "<p>Cette fonction récupère tous les prêts avec les détails de l'item, de l'emprunteur et du prêteur.</p>";

// Exécution de la méthode getAllWithDetails() qui retourne tous les prêts avec détails
$pretsWithDetails = $pretModel->getAllWithDetails();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de prêts trouvés: " . count($pretsWithDetails) . "</p>";
echo "<pre>";
// Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
$displayPrets = array_slice($pretsWithDetails, 0, 5);
print_r($displayPrets);
if (count($pretsWithDetails) > 5) {
    echo "... (et " . (count($pretsWithDetails) - 5) . " autres prêts)";
}
echo "</pre>";

// Test 4: Récupération des prêts actifs
echo "<h2>Test 4: Récupération des prêts actifs</h2>";
echo "<p>Cette fonction récupère tous les prêts qui n'ont pas encore été retournés.</p>";

// Exécution de la méthode getActiveLoans() qui retourne tous les prêts actifs
$activeLoans = $pretModel->getActiveLoans();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de prêts actifs: " . count($activeLoans) . "</p>";
echo "<pre>";
// Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
$displayPrets = array_slice($activeLoans, 0, 5);
print_r($displayPrets);
if (count($activeLoans) > 5) {
    echo "... (et " . (count($activeLoans) - 5) . " autres prêts)";
}
echo "</pre>";

// Test 5: Récupération des prêts en retard
echo "<h2>Test 5: Récupération des prêts en retard</h2>";
echo "<p>Cette fonction récupère tous les prêts dont la date de retour prévue est dépassée mais qui n'ont pas encore été retournés.</p>";

// Exécution de la méthode getOverdueLoans() qui retourne tous les prêts en retard
$overdueLoans = $pretModel->getOverdueLoans();

// Affichage des résultats
echo "<h3>Résultats:</h3>";
echo "<p>Nombre de prêts en retard: " . count($overdueLoans) . "</p>";
echo "<pre>";
// Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
$displayPrets = array_slice($overdueLoans, 0, 5);
print_r($displayPrets);
if (count($overdueLoans) > 5) {
    echo "... (et " . (count($overdueLoans) - 5) . " autres prêts)";
}
echo "</pre>";

// Test 6: Récupération des prêts par emprunteur
echo "<h2>Test 6: Récupération des prêts par emprunteur</h2>";
echo "<p>Cette fonction récupère tous les prêts associés à un emprunteur spécifique.</p>";

// Récupération d'une instance du modèle Emprunteur pour obtenir un emprunteur existant
$emprunteurModel = new Models\Emprunteur();
$emprunteurs = $emprunteurModel->getAll();

// Vérification qu'il y a au moins un emprunteur dans la base de données
if (count($emprunteurs) > 0) {
    // Récupération du premier ID d'emprunteur
    $emprunteurId = $emprunteurs[0]['id'];

    // Exécution de la méthode getLoansByBorrower() pour récupérer les prêts de cet emprunteur
    $loansByBorrower = $pretModel->getLoansByBorrower($emprunteurId);

    // Affichage des résultats
    echo "<h3>Prêts de l'emprunteur avec ID $emprunteurId:</h3>";
    echo "<p>Nombre de prêts trouvés: " . count($loansByBorrower) . "</p>";
    if (count($loansByBorrower) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
        $displayPrets = array_slice($loansByBorrower, 0, 5);
        print_r($displayPrets);
        if (count($loansByBorrower) > 5) {
            echo "... (et " . (count($loansByBorrower) - 5) . " autres prêts)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun prêt trouvé pour cet emprunteur.</p>";
    }
} else {
    echo "<p>Aucun emprunteur trouvé dans la base de données.</p>";
}

// Test 7: Récupération des prêts par item
echo "<h2>Test 7: Récupération des prêts par item</h2>";
echo "<p>Cette fonction récupère tous les prêts associés à un item spécifique.</p>";

// Récupération d'une instance du modèle Item pour obtenir un item existant
$itemModel = new Models\Item();
$items = $itemModel->getAll();

// Vérification qu'il y a au moins un item dans la base de données
if (count($items) > 0) {
    // Récupération du premier ID d'item
    $itemId = $items[0]['id'];

    // Exécution de la méthode getLoansByItem() pour récupérer les prêts de cet item
    $loansByItem = $pretModel->getLoansByItem($itemId);

    // Affichage des résultats
    echo "<h3>Prêts de l'item avec ID $itemId:</h3>";
    echo "<p>Nombre de prêts trouvés: " . count($loansByItem) . "</p>";
    if (count($loansByItem) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
        $displayPrets = array_slice($loansByItem, 0, 5);
        print_r($displayPrets);
        if (count($loansByItem) > 5) {
            echo "... (et " . (count($loansByItem) - 5) . " autres prêts)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun prêt trouvé pour cet item.</p>";
    }
} else {
    echo "<p>Aucun item trouvé dans la base de données.</p>";
}

// Test 8: Récupération de l'historique des prêts d'un item
echo "<h2>Test 8: Récupération de l'historique des prêts d'un item</h2>";
echo "<p>Cette fonction récupère l'historique complet des prêts pour un item spécifique, triés par date (du plus récent au plus ancien).</p>";

// Vérification qu'il y a au moins un item dans la base de données
if (count($items) > 0) {
    // Récupération du premier ID d'item
    $itemId = $items[0]['id'];

    // Exécution de la méthode getItemLoanHistory() pour récupérer l'historique des prêts de cet item
    $itemLoanHistory = $pretModel->getItemLoanHistory($itemId);

    // Affichage des résultats
    echo "<h3>Historique des prêts de l'item avec ID $itemId:</h3>";
    echo "<p>Nombre de prêts trouvés: " . count($itemLoanHistory) . "</p>";
    if (count($itemLoanHistory) > 0) {
        echo "<pre>";
        // Affichage des 5 premiers prêts seulement pour éviter une sortie trop longue
        $displayPrets = array_slice($itemLoanHistory, 0, 5);
        print_r($displayPrets);
        if (count($itemLoanHistory) > 5) {
            echo "... (et " . (count($itemLoanHistory) - 5) . " autres prêts)";
        }
        echo "</pre>";
    } else {
        echo "<p>Aucun prêt trouvé dans l'historique de cet item.</p>";
    }
} else {
    echo "<p>Aucun item trouvé dans la base de données.</p>";
}

// Test 9: Récupération du prêt actif d'un item
echo "<h2>Test 9: Récupération du prêt actif d'un item</h2>";
echo "<p>Cette fonction récupère le prêt actif (non retourné) pour un item spécifique, s'il existe.</p>";

// Vérification qu'il y a au moins un item dans la base de données
if (count($items) > 0) {
    // Récupération du premier ID d'item
    $itemId = $items[0]['id'];

    // Exécution de la méthode getCurrentItemLoan() pour récupérer le prêt actif de cet item
    $currentItemLoan = $pretModel->getCurrentItemLoan($itemId);

    // Affichage des résultats
    echo "<h3>Prêt actif de l'item avec ID $itemId:</h3>";
    if ($currentItemLoan) {
        echo "<pre>";
        print_r($currentItemLoan);
        echo "</pre>";
    } else {
        echo "<p>Aucun prêt actif trouvé pour cet item. L'item est disponible.</p>";
    }
} else {
    echo "<p>Aucun item trouvé dans la base de données.</p>";
}

// Test 10: Création d'un nouveau prêt (commenté pour éviter de modifier la base de données)
echo "<h2>Test 10: Création d'un nouveau prêt</h2>";
echo "<p>Cette fonction crée un nouveau prêt dans la base de données.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la création d'un prêt.</p>";

/*
// Vérification qu'il y a au moins un item, un emprunteur et un administrateur dans la base de données
if (count($items) > 0 && count($emprunteurs) > 0) {
    // Récupération du premier ID d'item et d'emprunteur
    $itemId = $items[0]['id'];
    $emprunteurId = $emprunteurs[0]['id'];

    // Récupération d'un administrateur
    $adminModel = new Models\Administrateur();
    $admins = $adminModel->getAll();

    if (count($admins) > 0) {
        $adminId = $admins[0]['id'];

        // Exécution de la méthode createLoan() pour créer un nouveau prêt
        $newLoanId = $pretModel->createLoan(
            $itemId,
            $emprunteurId,
            $adminId,
            date('Y-m-d'), // Date de sortie (aujourd'hui)
            date('Y-m-d', strtotime('+2 weeks')), // Date de retour prévue (dans 2 semaines)
            'Test de création de prêt' // Note de début
        );

        // Affichage des résultats
        echo "<h3>Résultats de la création:</h3>";
        if ($newLoanId) {
            echo "<p>Nouveau prêt créé avec succès. ID: $newLoanId</p>";

            // Récupération du nouveau prêt pour vérification
            $newLoan = $pretModel->getById($newLoanId);
            echo "<pre>";
            print_r($newLoan);
            echo "</pre>";
        } else {
            echo "<p>Échec de la création du prêt.</p>";
        }
    } else {
        echo "<p>Aucun administrateur trouvé dans la base de données.</p>";
    }
} else {
    echo "<p>Aucun item ou emprunteur trouvé dans la base de données.</p>";
}
*/

// Test 11: Fin d'un prêt (commenté pour éviter de modifier la base de données)
echo "<h2>Test 11: Fin d'un prêt (retour d'item)</h2>";
echo "<p>Cette fonction met à jour un prêt pour indiquer que l'item a été retourné.</p>";
echo "<p><strong>Note:</strong> Ce code est commenté pour éviter de modifier la base de données. Décommentez-le pour tester la fin d'un prêt.</p>";

/*
// Récupération des prêts actifs
$activeLoans = $pretModel->getActiveLoans();

// Vérification qu'il y a au moins un prêt actif
if (count($activeLoans) > 0) {
    // Récupération du premier ID de prêt actif
    $activeLoanId = $activeLoans[0]['id'];

    // Exécution de la méthode endLoan() pour terminer le prêt
    $endLoanResult = $pretModel->endLoan(
        $activeLoanId,
        date('Y-m-d'), // Date de retour effective (aujourd'hui)
        'Test de fin de prêt - Item retourné en bon état' // Note de fin
    );

    // Affichage des résultats
    echo "<h3>Résultats de la fin du prêt:</h3>";
    if ($endLoanResult) {
        echo "<p>Prêt terminé avec succès. ID: $activeLoanId</p>";

        // Récupération du prêt mis à jour pour vérification
        $updatedLoan = $pretModel->getById($activeLoanId);
        echo "<pre>";
        print_r($updatedLoan);
        echo "</pre>";
    } else {
        echo "<p>Échec de la fin du prêt.</p>";
    }
} else {
    echo "<p>Aucun prêt actif trouvé dans la base de données.</p>";
}
*/

// Lien pour revenir à la page d'accueil des tests
echo "<p><a href='index.php'>Retour à la page d'accueil des tests</a></p>";
