<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si le nom de categorie est fourni
    if (!isset($_GET['categorie'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'nom de catégorie' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $categorieName = $_GET['categorie'];

    try{
        
        // instancier le model Categorie
        $categorieModel = new Models\Categorie();

        //récuperation Id du catégorie
        $categorieId = (int)$categorieModel->getByName($categorieName);

        if(!$categorieId){
            $response = [
                "success" => false,
                "message" => "Aucun categorie trouvé avec le nom fourni."
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
                "message" => "La suppression a échoué : la catégorie n'existe plus ou une erreur est survenue."
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