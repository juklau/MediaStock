<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    // Vérifier si le qr_code de l'item est fourni et valide
    // !is_numeric($_GET['qr_code']) || ||  (int)$_GET['id'] <= 0 ===> à remettre quand on changera le type de qr_code
    if (!isset($_GET['qr_code'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'qr_code' manquant ou invalide"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $qrCode = $_GET['qr_code'];

    try{

        // instancier le model Item
        $itemModel = new Models\Item();

        // obtenir les éléments d'une item
        $item = $itemModel->findByQrCode($qrCode);

        if($item){
            $response = [
                "success" => true,
                "data" => $item, 
                "message" => "Connexion réussi"
            ];
        }else{
            $response = [
                "success" => false,
                "message" => "Aucun donnée trouvée avec le QR code fourni."
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