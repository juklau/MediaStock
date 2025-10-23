<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['formation'])) {

        $response = [
            "success" => false,
            "message" => "Champ 'formation' obligatoire manque"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $name = $input['formation'];
   
    try{

       // instancier le model Formation
        $formationModel = new Models\Formation();

        $formationId = $formationModel->createFormation($name);

        if($formationId !== false){
            $response = [
                "success" => true,
                "formation_id" => $formationId, 
                "message" => "Formation créée avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la création de la formation"
            ];
        }

        // afficher en JSON le résultat value:.....; flags:.....
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