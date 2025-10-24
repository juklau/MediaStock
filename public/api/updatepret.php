-- Active: 1708044290264@@localhost@3306
<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    // $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    // if (!isset($input['date_sortie']) || 
    //     !isset($input['date_retour_prevue']) ||
    //     !isset($input['date_retour_effective']) ||
    //     !isset($input['note_debut']) ||
    //     !isset($input['note_fin'])) {
        

    //     $response = [
    //         "success" => false,
    //         "message" => "Champs obligatoires manquants: date_sortie, date_retour_prevue, date_retour_effective, note_debut, note_fin"
    //     ];
    //     echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    //     exit;
    // }

    // Récupération des données
    $dateSortie = new DateTime("2025-12-31"); #$input['date_sortie']
    $dateRetourPrevue = new DateTime("2025-12-31"); #$input['date_retour_prevue']
    $dateRetourEffective = new DateTime("2025-12-31"); #$input['date_retour_effective']
    $noteDebut = "test"; #$input;['note_debut']
    $noteFin = "test"; #$input['note_fin']

    try{

       // instancier le model Emprunteur
        $pretModel = new Models\Pret();
        $pretId = $pretModel->getLoanByItemId(id: 2);
        $pretUpdated = $pretModel->updateItemLoan($pretId, $dateSortie, $dateRetourPrevue, $dateRetourEffective, $noteDebut, $noteFin);

        if($pretUpdated !== false){
            $response = [
                "success" => true,
                "pret_id" => $pretUpdated, 
                "message" => "Pret mis à jour avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la mise à jour du pret"
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