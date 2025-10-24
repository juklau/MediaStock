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

    try{

        // instancier le model Administrateur
        $administrateurModel = new Models\Administrateur();

        //récuperation Id de l'admin
        $adminId = (int)$administrateurModel->getByName($adminLogin);

        if(!$adminId){
            $response = [
                "success" => false,
                "message" => "Aucun administrateur trouvé avec le nom fourni."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // Supprimer l'admin
        $delete = $administrateurModel->deleteAdmin($adminId);
       
        if($delete){
            $response = [
                "success" => true,
                // "data" => $delete, 
                "message" => "L'administrateur supprimé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "La suppression a échoué : l'administrateur n'existe plus ou une erreur est survenue."
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