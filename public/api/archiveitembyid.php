<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si l'id de l'item est fourni
    if (!isset($_GET['id'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre id de l'item manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $itemId = (int)$_GET['id'];

    try{

        // instancier le model Item
        $itemModel = new Models\Item();

        $item = $itemModel->getById($itemId);

        if(!$item){
            $response = [
                "success" => false,
                "message" => "Aucun item trouvé avec l'ID fourni."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }


        // archiver l'item
        $archived = $itemModel->archiveItem($itemId);

        if($archived){
            $response = [
                "success" => true,
                "message" => "L'item a été archivé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" =>  "L'archivage a échoué : une erreur est survenue."
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