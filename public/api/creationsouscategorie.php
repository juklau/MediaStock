<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['sous_categorie']) || 
        !isset($input['categorie_id'])) {

        $response = [
            "success" => false,
            "message" => "Champs obligatoires manquants: sous_categorie, categorie_id"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $name = $input['sous_categorie'];
    $categoryId = $input['categorie_id'];
   
    try{

       // instancier le model SousCategorie
        $sousCategorieModel = new Models\SousCategorie();

        $sousCategorieId = $sousCategorieModel->createSubcategory($name, $categoryId);

        if($sousCategorieId !== false){
            $response = [
                "success" => true,
                "sous_categorie_id" => $sousCategorieId, 
                "message" => "Sous-catégorie créée avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la création de la sous-catégorie"
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