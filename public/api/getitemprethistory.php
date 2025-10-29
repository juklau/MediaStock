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

        // Obtenir l'historique de prêt d'un article
        // selon item_id!!!!
        $pretHistory = $pretModel->getItemLoanHistory($itemId);

        if (!empty($pretHistory)){
            $response = [
                "success" => true,
                "data" => $pretHistory, 
                "message" => "Historique de prêt récupéré avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucun donnée de prêt trouvée pour cet article."
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
