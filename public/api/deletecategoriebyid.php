<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si l'id de categorie est fourni et valide
    if (!isset($_GET['id']) || !is_numeric($_GET['id']) || (int)$_GET['id'] <= 0) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'id' manquant ou invalide"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $categorieId = $_GET['id'];

    try{

        // instancier le model Categorie
        $categorieModel = new Models\Categorie();

        $categorie = $categorieModel->getById($categorieId);

        if(!$categorie){
            $response = [
                "success" => false,
                "message" => "Aucun categorie trouvé avec l'ID fourni."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // Supprimer une catégorie et toutes ses sous-catégories
        $delete = $categorieModel->deleteWithSubcategories($categorieId);
        // $delete = $categorieModel->deleteWithSubcategories(6);

        if($delete){
            $response = [
                "success" => true,
                // "data" => $delete, 
                "message" => "Suppression de la catégorie et de ses sous-catégories réussie"
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