<?php
session_start();

require_once __DIR__ . '/autoload.php';

if (isset($_SESSION['login']) && isset($_SESSION['mot_de_passe_hash'])) {
    $username = $_SESSION['login'] ?? '';
    $password = $_SESSION['mot_de_passe_hash'] ?? '';

    // instancier le model User
    $userModel = new Models\Administrateur();
    $admin = $userModel->authenticate($username, $password);

    try{

        if ($admin) {
            $_SESSION['login'] = $username;

            // Exemple d'utilisation de la couche d'accès aux données
            $response = [
                "success" => true,
                "data" => $admin, 
                "message" => "Connexion réussi"
            ];
            
                // afficher en JSON le résultat
                // echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            // Rediriger vers la page d'accueil ou tableau de bord
            header("Location: ../frontend/index.html");
            exit();
        } else {
            $response = [
                "success" => false,
                "message" => "Aucun donnée trouvée."
            ];
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
            // Afficher le message d'erreur sur la page de connexion
            echo $error;
        }
    }catch(PDOException $e){
        error_log("Erreur de connexion: " . $e->getMessage());
    $response = [
        "success" => false,
        "message" => "Erreur de connexion: " . $e->getMessage()
    ];

    // afficher en JSON le résultat
    // echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

?>