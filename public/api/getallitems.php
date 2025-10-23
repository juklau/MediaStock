<?php
require_once __DIR__ . '/../autoload.php';

// Afficher le résultat en JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Instancier le modèle Item
$itemModel = new Models\Item();

// Récupérer tous les items
$items = $itemModel->getAllItems();
echo json_encode($items);
?>
