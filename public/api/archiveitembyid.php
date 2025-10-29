<?php
    require_once __DIR__ . '/../autoload.php';

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    if (!isset($_GET['id'])) {
        echo json_encode([
            "success" => false,
            "message" => "Paramètre id manquant"
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $itemId = (int)$_GET['id'];
    if ($itemId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "ID invalide"
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    try {
        $itemModel = new Models\Item();

        $item = $itemModel->getItemByID($itemId);
        if (!$item) {
            echo json_encode([
                "success" => false,
                "message" => "Aucun item trouvé avec l'ID fourni."
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        // Vérifier qu'il n'y a pas de prêt actif
        if (!$itemModel->isAvailable($itemId)) {
            echo json_encode([
                "success" => false,
                "message" => "L'item ne peut pas être archivé car il est en cours de prêt."
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }

        $archived = $itemModel->archiveItem($itemId);

        if ($archived) {
            $response = ["success" => true, "message" => "L'item a été archivé avec succès"];
        } else {
            $response = ["success" => false, "message" => "L'archivage a échoué : une erreur est survenue."];
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Erreur : " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
?>