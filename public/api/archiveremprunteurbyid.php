<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

     // Vérifier si le nom de admin est fourni
    if (!isset($_GET['id'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre id de l'emprunteur manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $emprunteurId = (int)$_GET['id'];

    try{

        // instancier le model Emprunteur
        $emprunteurModel = new Models\Emprunteur();

        $emprunteur = $emprunteurModel->getById($emprunteurId);

        if(!$emprunteur){
            $response = [
                "success" => false,
                "message" => "Aucun emprunteur trouvé avec l'ID fourni."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        $activePrets = $emprunteurModel->getActiveLoans($emprunteurId);

        // Vérifier si l'emprunteur n'a pas un prêt en cours
        if (!empty($activePrets)) {
            $response = [
                "success" => false,
                "message" => "L'emprunteur ne peut pas être archivé car il a au moins un prêt en cours."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // archiver l'emprunteur
        $archived = $emprunteurModel->archiveEmprunteur($emprunteurId);

        if($archived){
            $response = [
                "success" => true,
                "message" => "L'emprunteur a été archivé avec succès"
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