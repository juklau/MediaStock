<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si le searchTerm (disponible ou indisponible ou retard) est fourni
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

        // instancier le model Item, le model Pret
        $itemModel = new Models\Item();
        $pretModel = new Models\Pret();

        $disponibles = $itemModel->getAvailableItemNames();
        $indisponibles = $itemModel->afficheItemIndisponible();
        $enretards = $pretModel->getOverdueLoans();

        // construction un tableau associatif pour éviter les doublons
        $result = [];

        if($searchTerm === "disponible"){
            // ajouter les disponibles
            foreach($disponibles as $item){
                $key = $item['image_url'] . '|' . $item['id'] . '|' . $item['nom'] . '|' . $item['model'];
                $result[$key] = [
                    "image_url" => $item['image_url'],
                    "id" => $item['id'],
                    "nom" => $item['nom'],
                    "model" => $item['model'],
                    "statut" => "disponible"
                ];
            }
        }elseif($searchTerm === "indisponible"){
            // ajouter les indisponibles
            foreach($indisponibles as $item){
                $key = $item['image_url'] . '|' . $item['id'] . '|' . $item['nom'] . '|' . $item['model'];

                // pour éviter que ça soit écraser
                if(!isset($result[$key])){
                    $result[$key] = [
                        "image_url" => $item['image_url'],
                        "id" => $item['id'],
                        "nom" => $item['nom'],
                        "model" => $item['model'],
                        "statut" => "indisponible",
                        "date_retour_prévu" => $item['date_retour_prevue']
                    ];
                }
            }
        }elseif($searchTerm === "retard"){
            // ajouter les items qui sont en retard
            foreach($enretards as $pret){
                $key = $pret['image_url'] . '|' . $pret['id'] . '|' . $pret['item_nom'] . '|' . $pret['item_model'];

                $result[$key] = [
                    "image_url" => $pret['image_url'],
                    "id" => $pret['id'],
                    "nom" => $pret['item_nom'],
                    "model" => $pret['item_model'],
                    "statut" => "en retard",
                    "date_retour_prévu" => $pret['date_retour_prevue']
                ];
            }
        }

        $response = [
            "success" => true,
            "data" => array_values($result), 
            "message" => "Connexion réussi"
        ];
        

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