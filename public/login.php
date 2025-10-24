<?php
session_start();

require_once __DIR__ . '/autoload.php';

try{

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            header('Content-Type: application/json');
            $response = [
                "success" => false,
                "title" => "Erreur de connexion",
                "message" => "Le nom d'utilisateur et le mot de passe ne peuvent pas être vides."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit();
        }
    
        // instancier le model User
        $userModel = new Models\Administrateur();
        $admin = $userModel->authenticate($username, $password);
        
        if ($admin) {
            $_SESSION['username'] = $username;

            // Exemple d'utilisation de la couche d'accès aux données
            header('Content-Type: application/json');
            $response = [
                "success" => true,
                "title" => "Connexion réussie",
                "data" => $admin, 
                "message" => "Vous serez redirigé vers la page d\'accueil."
            ];
            

            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            // afficher en JSON le résultat
            // Rediriger vers la page d'accueil ou tableau de bord
            exit();
        } else {

            header('Content-Type: application/json');
            $response = [
                "success" => false,
                "title" => "Erreur de connexion",
                "message" => "Login ou mot de passe incorrect."
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // $error = "Nom d'utilisateur ou mot de passe incorrect.";
            // Afficher le message d'erreur sur la page de connexion
            // echo $error;
        }
    }else {

        header('Content-Type: application/json');
        $response = [
            "success" => false,
            "title" => "Erreur de connexion",
            "message" => "Les informations de connexion ne sont pas définies dans la session."
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}catch(PDOException $e){
    error_log("Erreur de connexion: " . $e->getMessage());
    $response = [
        "success" => false,
        "message" => "Erreur de connexion: " . $e->getMessage()
    ];

}
    
?>
