<?php
/**
 * Page d'accueil des tests MediaStock
 * 
 * Cette page fournit des liens vers tous les tests individuels et permet d'exécuter tous les tests à la fois.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tests MediaStock</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        a {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .description {
            color: #666;
            margin-top: 5px;
        }
        .run-all {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            font-weight: bold;
        }
        .run-all:hover {
            background-color: #45a049;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Tests MediaStock</h1>
    
    <p>Cette page contient des liens vers tous les tests de la couche d'accès aux données MediaStock. Vous pouvez exécuter chaque test individuellement ou tous les tests à la fois.</p>
    
    <a href="test_all.php" class="run-all">Exécuter tous les tests</a>
    
    <h2>Tests individuels</h2>
    
    <ul>
        <li>
            <a href="test_administrateur.php">Test du modèle Administrateur</a>
            <div class="description">Teste les fonctionnalités liées aux administrateurs (authentification, gestion des comptes, etc.)</div>
        </li>
        <li>
            <a href="test_item.php">Test du modèle Item</a>
            <div class="description">Teste les fonctionnalités liées aux items (recherche, filtrage par catégorie, etc.)</div>
        </li>
        <li>
            <a href="test_pret.php">Test du modèle Pret</a>
            <div class="description">Teste les fonctionnalités liées aux prêts (création, fin de prêt, prêts actifs, etc.)</div>
        </li>
        <li>
            <a href="test_emprunteur.php">Test du modèle Emprunteur</a>
            <div class="description">Teste les fonctionnalités liées aux emprunteurs (recherche, filtrage par formation, etc.)</div>
        </li>
        <li>
            <a href="test_categorie.php">Test du modèle Categorie</a>
            <div class="description">Teste les fonctionnalités liées aux catégories (sous-catégories, items par catégorie, etc.)</div>
        </li>
        <li>
            <a href="test_formation.php">Test du modèle Formation</a>
            <div class="description">Teste les fonctionnalités liées aux formations (emprunteurs par formation, statistiques, etc.)</div>
        </li>
        <li>
            <a href="test_sous_categorie.php">Test du modèle SousCategorie</a>
            <div class="description">Teste les fonctionnalités liées aux sous-catégories (items par sous-catégorie, etc.)</div>
        </li>
    </ul>
    
    <h2>Documentation</h2>
    
    <p>Pour plus d'informations sur la couche d'accès aux données MediaStock, consultez le fichier README.md à la racine du projet.</p>
    
    <p><a href="../index.php">Retour à la page d'accueil du projet</a></p>
</body>
</html>