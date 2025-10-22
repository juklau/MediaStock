<?php
/**
 * Exécution de tous les tests MediaStock
 * 
 * Ce script exécute tous les tests de la couche d'accès aux données MediaStock en séquence
 * et affiche les résultats dans une seule page.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les tests MediaStock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
            border-top: 1px solid #ccc;
            padding-top: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 5px solid #4CAF50;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .back-link {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #0066cc;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
        }
        .back-link:hover {
            background-color: #0055aa;
        }
    </style>
</head>
<body>
    <h1>Exécution de tous les tests MediaStock</h1>
    
    <p>Cette page exécute tous les tests de la couche d'accès aux données MediaStock en séquence.</p>
    
    <div class="test-section">
        <h2>Test du modèle Administrateur</h2>
        <?php
        // Démarrage de la capture de sortie
        ob_start();
        // Inclusion du test Administrateur
        include('test_administrateur.php');
        // Récupération de la sortie et nettoyage
        $output = ob_get_clean();
        // Suppression des liens de retour et des balises HTML de base
        $output = preg_replace('/<p><a href=\'index.php\'>.*?<\/a><\/p>/', '', $output);
        $output = preg_replace('/<h1>.*?<\/h1>/', '', $output);
        echo $output;
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test du modèle Item</h2>
        <?php
        // Démarrage de la capture de sortie
        ob_start();
        // Inclusion du test Item
        include('test_item.php');
        // Récupération de la sortie et nettoyage
        $output = ob_get_clean();
        // Suppression des liens de retour et des balises HTML de base
        $output = preg_replace('/<p><a href=\'index.php\'>.*?<\/a><\/p>/', '', $output);
        $output = preg_replace('/<h1>.*?<\/h1>/', '', $output);
        echo $output;
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test du modèle Pret</h2>
        <?php
        // Démarrage de la capture de sortie
        ob_start();
        // Inclusion du test Pret
        include('test_pret.php');
        // Récupération de la sortie et nettoyage
        $output = ob_get_clean();
        // Suppression des liens de retour et des balises HTML de base
        $output = preg_replace('/<p><a href=\'index.php\'>.*?<\/a><\/p>/', '', $output);
        $output = preg_replace('/<h1>.*?<\/h1>/', '', $output);
        echo $output;
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test du modèle Emprunteur</h2>
        <?php
        // Démarrage de la capture de sortie
        ob_start();
        // Inclusion du test Emprunteur
        include('test_emprunteur.php');
        // Récupération de la sortie et nettoyage
        $output = ob_get_clean();
        // Suppression des liens de retour et des balises HTML de base
        $output = preg_replace('/<p><a href=\'index.php\'>.*?<\/a><\/p>/', '', $output);
        $output = preg_replace('/<h1>.*?<\/h1>/', '', $output);
        echo $output;
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test du modèle Categorie</h2>
        <?php
        // Démarrage de la capture de sortie
        ob_start();
        // Inclusion du test Categorie
        include('test_categorie.php');
        // Récupération de la sortie et nettoyage
        $output = ob_get_clean();
        // Suppression des liens de retour et des balises HTML de base
        $output = preg_replace('/<p><a href=\'index.php\'>.*?<\/a><\/p>/', '', $output);
        $output = preg_replace('/<h1>.*?<\/h1>/', '', $output);
        echo $output;
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test du modèle Formation</h2>
        <?php
        // Démarrage de la capture de sortie
        ob_start();
        // Inclusion du test Formation
        include('test_formation.php');
        // Récupération de la sortie et nettoyage
        $output = ob_get_clean();
        // Suppression des liens de retour et des balises HTML de base
        $output = preg_replace('/<p><a href=\'index.php\'>.*?<\/a><\/p>/', '', $output);
        $output = preg_replace('/<h1>.*?<\/h1>/', '', $output);
        echo $output;
        ?>
    </div>
    
    <div class="test-section">
        <h2>Test du modèle SousCategorie</h2>
        <?php
        // Démarrage de la capture de sortie
        ob_start();
        // Inclusion du test SousCategorie
        include('test_sous_categorie.php');
        // Récupération de la sortie et nettoyage
        $output = ob_get_clean();
        // Suppression des liens de retour et des balises HTML de base
        $output = preg_replace('/<p><a href=\'index.php\'>.*?<\/a><\/p>/', '', $output);
        $output = preg_replace('/<h1>.*?<\/h1>/', '', $output);
        echo $output;
        ?>
    </div>
    
    <a href="index.php" class="back-link">Retour à la page d'accueil des tests</a>
</body>
</html>