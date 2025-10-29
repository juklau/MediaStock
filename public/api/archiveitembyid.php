<?php
<?php
require_once __DIR__ . '/../autoload.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$id = null;
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : null;
}

if (!$id || $id <= 0) {
    echo json_encode(["success" => false, "message" => "ID manquant ou invalide"], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $itemModel = new Models\Item();

    $item = $itemModel->getItemByID($id);
    if (!$item) {
        echo json_encode(["success" => false, "message" => "Aucun item trouvé avec l'ID fourni."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!$itemModel->isAvailable($id)) {
        echo json_encode(["success" => false, "message" => "L'item est en prêt actif et ne peut pas être archivé."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $archived = $itemModel->archiveItem($id);

    if ($archived) {
        echo json_encode(["success" => true, "message" => "Item archivé avec succès", "id" => $id], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["success" => false, "message" => "L'archivage a échoué (0 rows affected)."], JSON_UNESCAPED_UNICODE);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Exception: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>