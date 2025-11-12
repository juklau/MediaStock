<?php
    require_once __DIR__ . '/../autoload.php';

    // à vérifier si ca marche!!!!

    header('Content-Type: application/json'); 
    header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Methods: POST');

    // lire le contenu JSON envoyé
    $input = json_decode(file_get_contents('php://input'), true);

    // Vérifier si les éléments obligatoires sont fournis
    if (!isset($input['emprunteur_nom']) || 
        !isset($input['emprunteur_prenom']) || 
        !isset($input['role'])) {

        $response = [
            "success" => false,
            "message" => "Champs obligatoires manquants: emprunteur_nom, emprunteur_prenom, role"
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    $nom = htmlspecialchars(trim($input['emprunteur_nom']));
    $prenom = htmlspecialchars(trim($input['emprunteur_prenom']));
    $role= htmlspecialchars(trim($input['role']));
    $formationId = $input['formation_id'] ?? null;

   
    try{

        // instancier le model Emprunteur
        $emprunteurModel = new Models\Emprunteur();

        $existing = $emprunteurModel->findExistingEmprunteur($nom, $prenom);

        if($existing){
            // il est déjà dans la bdd
            $emprunteurId = (int)$existing['id'];

            $response = [
                "success" => true,
                "emprunteur_id" => $emprunteurId, 
                "message" => "Emprunteur déjà existant dans la base."
            ];
        }else{
            // il faut créer emprunteur
            $emprunteurId = $emprunteurModel->addEmprunteur($nom, $prenom, $role, $formationId);

            $response = [
                "success" => true,
                "emprunteur_id" => $emprunteurId, 
                "message" => "Nouvel emprunteur ajouté avec succès"
            ];
        }

        // afficher en JSON le résultat value:.....; flags:.....
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }catch (InvalidArgumentException $e) {

        $response = [
            "success" => false,
            "message" => "Erreur de validation : " . $e->getMessage()
        ];

        // Gestion des erreurs métier comme rôle ou formation invalide
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    } catch(PDOException $e){
        $response = [
            "success" => false,
            "message" => "Erreur de connexion: " . $e->getMessage()
        ];

        // afficher en JSON le résultat
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {

        // Autres erreurs
        $response = [
            "success" => false,
            "message" => "Erreur inattendue : " . $e->getMessage()
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

?>