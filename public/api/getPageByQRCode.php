<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    if (!isset($_GET['code'])) {
        $response = [
            "success" => false,
            "message" => "Paramètre 'code' manquant"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $qrCode = $_GET['code'];

    try {
        $itemModel = new Models\Item();
        $pretModel = new Models\Pret();

        // 1. Trouver l'item par son QR code
        $item = $itemModel->findByQrCode($qrCode);

        if (!$item) {
            $response = [
                "success" => false,
                "message" => "Aucun matériel trouvé avec ce QR code"
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // 2. Récupérer tous les prêts actifs avec getActiveLoans()
        $activeLoans = $pretModel->getActiveLoans();

        // 3. Vérifier si l'item_id est dans les prêts actifs
        $itemEstPrete = false;
        foreach ($activeLoans as $loan) {
            if ($loan['item_id'] == $item['id']) {
                $itemEstPrete = true;
                break;
            }
        }

        // 4. Décider la redirection selon le statut
        if ($itemEstPrete) {
            $targetPage = "restitution.php";
            $message = "Matériel actuellement emprunté - redirection vers restitution";
        } else {
            $targetPage = "creation-pret.php";
            $message = "Matériel disponible - redirection vers création de prêt";
        }

        $response = [
            "success" => true,
            "targetPage" => $targetPage,
            "message" => $message,
            "item" => $item
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    } catch(PDOException $e) {
        $response = [
            "success" => false,
            "message" => "Erreur de connexion: " . $e->getMessage()
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
?>
