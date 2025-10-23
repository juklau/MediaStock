<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si l'ID du prêt est fourni
    if (!isset($input['id']) || !is_numeric($input['id']) || (int)$input['id'] <= 0) {

        $response = [
            "success" => false,
            "message" => "Paramètre 'id' du prêt manquant ou invalide"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $pretId = (int)$input['id'];
    

    // Construire dynamiquement les champs à mettre à jour
    $data = [];

    if (isset($input['date_sortie'])) {
        $data['date_sortie'] = $input['date_sortie'];
    }

    if (isset($input['date_retour_prevue'])) {
        $data['date_retour_prevue'] = $input['date_retour_prevue'];
    }
    
    if (isset($input['note_debut'])) {
        $data['note_debut'] = $input['note_debut'];
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

       // instancier le model Pret
        $pretModel = new Models\Pret();

        //vérification si le prêt existe
        $pret = $pretModel->getById($pretId);

        if (!$pret){
            $response = [
            "success" => false,
            "message" => "Prêt introuvable"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
        }


        // Vérifier que le prêt est encore actif
        // s'il n'est pas actif => on ne peut pas modifier => pour préserver l'intégrité
        if (!empty($pret['date_retour_effective'])) {
            $response = [
                "success" => false,
                "message" => "Le prêt est déjà clôturé et ne peut pas être modifié."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }


        //mise à jour du prêt
        $pretUpdated = $pretModel->update($pretId, $data);

        if($pretUpdated){
            $response = [
                "success" => true,
                "pret_id" => $pretId, 
                "message" => "Prêt mis à jour avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la mise à jour du prêt"
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