
<!-- temporaire -->

<?php 
    use Config\Database;
    echo "<h1> Ajouter un emprunteur </h1>";
    
    try{
        //Connexion => récupération l'objet $pdo retourné par config.database.php
        // /** @var PDO $pdo */
        // $pdo = require __DIR__ . '/../config/database.php';


        require_once __DIR__ . '/../config/database.php';

        $pdo = Database::getInstance()->getConnection();
        $repo = new ItemRepository($pdo);

        //Repository
        require_once __DIR__ ."/../src/ItemRepository.php";
        $repo = new ItemRepository($pdo);



        //Verification function addEmprunteur
        // $id = $repo->addEmprunteur("Paris", "Michelle", "intervenant", null);

        // echo "<p>OK — nouvel emprunteur ID = " . htmlspecialchars((string)$id) . "</p>";

        //vérification de la fonction d'ajouter un Item
        // $repo->addItem("table", "model-4pattes", 18 , "mediastock/public/images/mp3-player.png", "bon", 4);
        // echo "<p> Item était bien ajouté </p>";

        //vérification de la fonction getAllItems
        // $allItems = $repo->getAllItems();
        // print_r($allItems);

         //vérification de la fonction getItemByID
         $repo->getItemByID(15);
         print_r($repo); 

        // $repo->getWithSubcategories($id);
        // print_r($repo);
        
    }catch(PDOException $e){
         echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    }

// phpinfo();
?>