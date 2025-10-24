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

    $name = $_GET['nom'];

    try{

        // instancier le model Categorie
        $categorieModel = new Models\Categorie();

        // obtenir les éléments d'une item
        $Id = $categorieModel->getByName($name);

        if($Id){
            $response = [
                "success" => true,
                "categorie_id" => $Id, 
                "message" => "Connexion réussi"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucune donnée trouvée avec le nom fourni."
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
