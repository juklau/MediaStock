<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['item_id']) || 
        !isset($input['emprunteur_id']) || 
        // !isset($input['date_sortie']) ||
        // !isset($input['date_retour_prevue']) ||
        !isset($input['note_debut']) ||
        !isset($input['note_fin']) ||
        !isset($input['preteur_id'])) {

        $response = [
            "success" => false,
            "message" => "Champs obligatoires manquants: item_id, emprunteur_id, date_sortie, date_retour_prevue,
                        note_debut, note_fin, preteur_id"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $itemId = (int)$input['item_id'];
    $emprunteurId = (int)$input['emprunteur_id'];
    $dateSortie = $input['date_sortie'] ?? null;
    $dateRetourPrevu = $input['date_retour_prevue'] ?? null;
    $noteDebut = $input['note_debut'];
    $noteFin = $input['note_fin'];
    $preteurId = (int)$input['preteur_id'];

   
    try{

        // instancier le model Pret
        $pretModel = new Models\Pret();


        // Vérifier si l'item est disponible
        if (!$pretModel->isAvailable($itemId)) {
            $response = [
                "success" => false,
                "message" => "L'article n'est pas disponible pour le prêt : il est déjà emprunté."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }


        $loanId = $pretModel->createLoan($itemId, $emprunteurId, $preteurId, $dateSortie, $dateRetourPrevue , $noteDebut);

        if($loanId !== false){
            $response = [
                "success" => true,
                "loan_id" => $loanId, 
                "message" => "Prêt créé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la création du prêt"
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