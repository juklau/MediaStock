<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si le rôle d'emprunter est fourni
    if (!isset($_GET['role'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'role' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $role = $_GET['role'];

    try{

        // instancier le model Emprunteur
        $emprunteurModel = new Models\Emprunteur();

        // obtenir les éléments des items
        $emprunteurs = $emprunteurModel->getByRole2($role);

        if($emprunteurs){
            $response = [
                "success" => true,
                "data" => $emprunteurs, 
                "message" => "Connexion réussi"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucun donnée trouvée."
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