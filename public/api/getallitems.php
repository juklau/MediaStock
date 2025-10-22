<?php
  require '../autoload.php';

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');

    $itemModel = new Models\Item();

    $item = $itemModel->getAll();
    echo json_encode($item);
    // return $stmt->fetch(\PDO::FETCH_ASSOC);

?>