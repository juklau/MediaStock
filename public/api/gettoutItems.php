<?php
require_once __DIR__ . '/../autoload.php';

<<<<<<< HEAD:public/api/getallitems.php
// Afficher le résultat en JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Instancier le modèle Item
$itemModel = new Models\Item();

// Récupérer tous les items
$items = $itemModel->getAllItems();
echo json_encode($items);
=======
    // Afficher le résultat en JSON
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Instancier le modèle Item
    $itemModel = new Models\Item();

    // Récupérer tous les items
    $items = $itemModel->getAllItems();
    echo json_encode($items);
>>>>>>> 3c63ddff2a78296bc33ed8c2cafcb54aa26ff234:public/api/gettoutItems.php
?>
