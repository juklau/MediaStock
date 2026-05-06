<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    if (!isset($_GET['qr_code'])) {
        echo json_encode([
            "success" => false,
            "message" => "Paramètre 'qr_code' manquant"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $qrCode = $_GET['qr_code'];

    try {
        $pretModel = new Models\Pret();
        $pret = $pretModel->getLoanByItemQrCode($qrCode);

        if ($pret) {
            echo json_encode([
                "success" => true,
                "data" => $pret
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Aucun prêt actif trouvé pour ce QR Code."
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

    } catch(Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Erreur : " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
?>
