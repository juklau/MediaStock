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

    $sousCategorieId = $_GET['id'];

    try{

        // instancier le model SousCategorie
        $sousCategorieModel = new Models\SousCategorie();

        $sousCategorie = $sousCategorieModel->getById($sousCategorieId);

        if(!$sousCategorie){
            $response = [
                "success" => false,
                "message" => "Aucun sous-categorie trouvé avec l'ID fourni."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // Supprimer une sous-catégorie et toutes ses sous-catégories
        $delete = $sousCategorieModel->delete($sousCategorieId);
      

        if($delete){
            $response = [
                "success" => true,
                // "data" => $delete, 
                "message" => "Suppression de la sous-catégorie et de ses sous-catégories réussie"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "La suppression a échoué : la sous-catégorie n'existe plus ou une erreur est survenue."
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