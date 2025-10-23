<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['emprunteur_nom']) || 
        !isset($input['emprunteur_prenom']) || 
        !isset($input['role']) ||
        // !isset($input['formation_id'])
        ) {

        $response = [
            "success" => false,
            "message" => "Champs obligatoires manquants: emprunteur_nom, emprunteur_prenom, role"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $nom = $input['emprunteur_nom'];
    $prenom = $input['emprunteur_prenom'];
    $role= $input['role'];
    $formationId = $input['formation_id'] ?? null;

   
    try{

       // instancier le model Emprunteur
        $emprunteurModel = new Models\Emprunteur();

        $emprunteurId = $emprunteurModel->addEmprunteur($nom, $prenom, $role, $formationId);

        if($emprunteurId !== false){
            $response = [
                "success" => true,
                "emprunteur_id" => $emprunteurId, 
                "message" => "Emprunteur créé avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la création de l'emprunteur"
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