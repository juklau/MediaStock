<?php

    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si l'ID est fourni et valide
    if (!isset($_GET['nom'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'nom' manquant."
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

   
    $name = trim($_GET['nom']);

    try{

        // instancier le model Formation
        $formationModel = new Models\Formation();

        // obtenir les éléments d'une item
        $formationId = $formationModel->getByName($name);

        if($formationId !== false){
            $response = [
                "success" => true,
                "formation_id" => $formationId, 
                "message" => "Formation trouvée avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucune formation trouvée avec le nom fourni."
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
