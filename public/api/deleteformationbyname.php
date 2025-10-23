<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si le nom de formation est fourni
    if (!isset($_GET['formation'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'nom de formation' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $formationName = $_GET['formation'];

    try{

        // instancier le model Formation
        $formationModel = new Models\Formation();

        //récuperation Id du formation
        $formationId = (int)$formationModel->getByName($formationName);

        if(!$formationId){
            $response = [
                "success" => false,
                "message" => "Aucun formation trouvé avec le nom fourni."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // Supprimer une formation
        $delete = $formationModel->deleteFormation(int $formationId);
    

        if($delete){
            $response = [
                "success" => true,
                // "data" => $delete, 
                "message" => "Suppression de la formation réussie"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucune donnée trouvée avec l'ID fourni."
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