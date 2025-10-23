<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['login']) || 
        !isset($input['mot_de_passe_hash'])) {

        $response = [
            "success" => false,
            "message" => "Champs obligatoires manquants: login, mot_de_passe_hash"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }


   
    $login = $input['login'];
    $password = $input['mot_de_passe_hash'];

    //pour tester 
    // $login =  "test";
    // $password =  "test";

    try{

       // instancier le model Administrateur
        $administrateurModel = new Models\Administrateur();

        //récupération l'id du administrateur
        $administrateurId = $administrateurModel->getByName($login);

        if (!$administrateurId) {
            $response = [
                "success" => false,
                "message" => "Administrateur introuvable avec le login fourni."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        $adminNewPassword = $administrateurModel->updatePassword($administrateurId, $password);

        if($adminNewPassword !== false){
            $response = [
                "success" => true,
                "admin_id" => $administrateurId, 
                "message" => "Mot de passe mis à jour avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Échec de la mise à jour de l'admin"
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