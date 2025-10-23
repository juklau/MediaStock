<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si le nom de categorie est fourni
    if (!isset($_GET['sous_categorie'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'nom de sous-categorie' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $sousCategorieName = $_GET['sous_categorie'];

    try{
        
        // instancier le model Sous_categorie
        $sousCategorieModel = new Models\SousCategorie();

        //récuperation Id du catégorie
        $sousCategorieId = (int)$sousCategorieModel->getByName($sousCategorieName);

        if(!$sousCategorieId){
            $response = [
                "success" => false,
                "message" => "Aucun sous-categorie trouvé avec le nom fourni."
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