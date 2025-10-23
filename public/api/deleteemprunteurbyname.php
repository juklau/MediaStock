<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

     // Vérifier si le nom de admin est fourni
    if (!isset($_GET['login'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'login de l'admin' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $adminLogin = $_GET['login'];

    $emprunteurId = $_GET['id'];

    try{ //////////////////////////à finir!!!!!!!!!!!!!!!!!!!!!!!!!!!

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

        // Supprimer l'emprunteur
        $delete = $emprunteurModel->deleteEmprunteur($emprunteurId);
       
        if($delete){
            $response = [
                "success" => true,
                // "data" => $delete, 
                "message" => "L'emprunteur supprimé avec succès"
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