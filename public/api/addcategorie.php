<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['categorie'])) {

        $response = [
            "success" => false,
            "message" => "Champ 'categorie' obligatoire manque"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $name = $input['categorie'];
   
    try{

       // instancier le model Categorie
        $categorieModel = new Models\Categorie();

        // $categorieId = $categorieModel->createCategory("maison");
        $categorieId = $categorieModel->createCategory($name);

        if($categorieId !== false){
            $response = [
                "success" => true,
                "categorie_id" => $categorieId, 
                "message" => "Catégorie créée avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la création de la catégorie"
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