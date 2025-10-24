<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');


    // // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['id']) || !isset($input['qr_code'])) {

        $response = [
            "success" => false,
            "message" => "Champs obligatoires manquants: id, qr_code"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }


    $itemId = (int)$input['id'];
    $qrCode = $input['qr_code'];


    // Construire dynamiquement les champs à mettre à jour
    $data = [];

    if (isset($input['nom'])) {
        $data['nom'] = $input['nom'];
    }
    if (array_key_exists('model', $input)) {
        $data['model'] = $input['model']; // peut être null
    }
    if (isset($input['image_url'])) {
        $data['image_url'] = $input['image_url'];
    }
    if (isset($input['etat'])) {
        $data['etat'] = $input['etat'];
    }
    
    if (isset($input['categorie_id'])) {
        $data['categorie_id'] = (int)$input['categorie_id'];
    }

    if(empty($data)){
        $response = [
            "success" => false,
            "message" => "Aucun champ à mettre à jour fourni"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    

    try{

        // instancier le model Item
        $itemModel = new Models\Item();

        //vérification si l'item existe 
        $item = $itemModel->getById($itemId);

        if (!$item){
            $response = [
            "success" => false,
            "message" => "Item introuvable"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
        }

        //mise à jour de l'item
        $itemUpdated = $itemModel->update($itemId, $data);

        if($itemUpdated){
            $response = [
                "success" => true,
                "item_id" => $itemId, 
                "message" => "Item mis à jour avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la mise à jour de l'item"
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