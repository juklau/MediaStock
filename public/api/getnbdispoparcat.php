<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // instancier le model Item
    $itemModel = new Models\Item();

    // obtenir tous les items
    $items = $itemModel->countAvailableItemsByCategory();

    // afficher en JSON le résultat
    echo json_encode($items);
?>