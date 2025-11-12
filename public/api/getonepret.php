<?php

    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si l'ID est fourni et valide
    if (!isset($_GET['id']) || !is_numeric($_GET['id']) || (int)$_GET['id'] <= 0) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'id' manquant ou invalide"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $itemId = (int)$_GET['id'];
    

    try{

        // instancier le model Pret
        $pretModel = new Models\Pret();

        // obtenir les éléments d'une item
        $pret = $pretModel->getLoanByItemId($itemId);

        if($pret){
            $response = [
                "success" => true,
                "data" => $pret, 
                "message" => "Prêt trouvé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucun prêt trouvé avec l'Id fourni."
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
