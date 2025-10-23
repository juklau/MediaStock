<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier les paramètres
    if (!isset($input['id']) || !is_numeric($input['id']) || (int)$input['id'] <= 0) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'id' manquant ou invalide"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    if (!isset($input['note_fin'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'note_fin' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $itemId = (int)$input['id'];  //=>selon item_id et pas id de prêt!!!
    $noteFin = $input['note_fin'];

    try{

        // instancier le model Pret
        $pretModel = new Models\Pret();

        // Vérifier s'il y a un prêt actif pour cet item
        $pretActif = $pretModel->getCurrentItemLoan($itemId);

        if(!$pretActif){
            $response = [
                "success" => false,
                "message" => "Aucun prêt actif trouvé pour cet article."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // clôturer le prêt
        $pretClotured = $pretModel->endLoan($pretActif['id'], null, $noteFin);

        if($pretClotured){
            $response = [
                "success" => true,
                "message" => "Le prêt a été clôturé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "La clôture a échoué : une erreur est survenue."
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