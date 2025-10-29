<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si l'id de l'item est fourni et valide
    if (!isset($_GET['id']) || !is_numeric($_GET['id']) || (int)$_GET['id'] <= 0) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'id' manquant ou invalide"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $itemId = $_GET['id'];

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

        // Supprimer l'item sans vérifier sa disponibilité!!!!
        $delete = $itemModel->delete($itemId);
       
        if($delete){
            $response = [
                "success" => true,
                // "data" => $delete, 
                "message" => "L'item supprimé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "La suppression a échoué : l'item n'existe plus ou une erreur est survenue."
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