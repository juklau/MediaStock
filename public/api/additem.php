<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['nom']) || 
        // !isset($input['model']) || 
        !isset($input['qr_code']) ||
        !isset($input['image_url']) ||
        !isset($input['etat']) ||
        !isset($input['categorie_id'])) {

        $response = [
            "success" => false,
            "message" => "Champs obligatoires manquants: nom, qr_code, image_url, etat , categorie_id,"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $nom = $input['nom'];
    $model = $input['model'] ?? null;
    $QRCode = $input['qr_code'];
    $imgUrl = $input['image_url'];
    $etat = $input['etat'];
    $categorieId = (int)$input['categorie_id'];

   
    try{

        // instancier le model Item
        $itemModel = new Models\Item();

        $itemId = $itemModel->addItem($nom, $model, $QRCode, $imgUrl, $etat, $categorieId);

        if($itemId !== false){
            $response = [
                "success" => true,
                "item_id" => $itemId, 
                "message" => "Item créé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la création de l'item"
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