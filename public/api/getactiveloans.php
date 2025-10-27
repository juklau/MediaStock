<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

   
    try{

        // instancier le model Pret
        $pretModel = new Models\Pret();
       
        // Obtenir tous les prêts actifs (non retournés)
        $pretsEnCours = $pretModel->getActiveLoans();

        if($pretsEnCours > 0){
            $response = [
                "success" => true,
                "data" => $pretsEnCours, 
                "message" => "Prêts actifs récupérés avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucun prêt actif trouvé"
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
