<?php
      require '../autoload.php';

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');

        $itemModel = new Models\Item();
        $data = $itemModel->getItemByID((int)$_GET['id']);

        try {

          if (isset($_GET['id'])) {

            if ($data) {
                $response = [
                    "success" => true,
                    "data" => $data,
                    "message" => "Connexion réussie"
                ];
            } else {
                $response = [
                    "success" => false,
                    "message" => "Aucune donnée trouvée"
                ];
            }
          } else {
            $response = [
                "success" => false,
                "message" => "ID non fourni"
            ];
          }

        // Sortie JSON
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);  
        } catch (PDOException $e) {
            $response = [
                "success" => false,
                "message" => "Erreur de connexion : " . $e->getMessage()
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
?>