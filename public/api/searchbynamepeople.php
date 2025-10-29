<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si le searchTerm (partie de nom) est fourni
    if (!isset($_GET['search_term'])) {  //=>p.ex. Ma
        $response = [
            "success" => false,
            "message" => "Paramètre 'search_term' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $searchTerm = $_GET['search_term'];

    try{

        // instancier le model Emprunteur
        $emprunteurModel = new Models\Emprunteur();

        // obtenir les éléments d'une item
        $emprunteurs = $emprunteurModel->searchByName($searchTerm);

        if($emprunteurs){
            $response = [
                "success" => true,
                "data" => $emprunteurs, 
                "message" => "Emprunteur trouvé avec le terme cherché avec succès"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucun emprunteur trouvé avec le terme cherché."
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