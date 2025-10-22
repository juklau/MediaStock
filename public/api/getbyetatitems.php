<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si l'état de l'item est fourni
    if (!isset($_GET['etat'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'etat' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $etat = $_GET['etat'];

    try{

        // instancier le model Item
        $itemModel = new Models\Item();

        // obtenir les éléments des items
        $items = $itemModel->getByCondition($etat);

        if($items){
            $response = [
                "success" => true,
                "data" => $items, 
                "message" => "Connexion réussi"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucun donnée trouvée."
            ];
        }

        // afficher en JSON le résultat
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }catch(PDOException $e){
        $response = [
            "success" => false,
            "message" => "Erreur de connexion: " . $e->getMessage()
        ];

        // afficher en JSON le résultat
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

?>