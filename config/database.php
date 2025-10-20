<?php
declare(strict_types=1);

    //connexion PDO à la base applicative

    // function database(): PDO{

        $host = getenv('DB_HOST') ?: 'mysql'; // 'mysql' est le nom du service dans docker-compose.yml
        $db   = getenv('DB_NAME') ?: 'mediastock';
        $user = getenv('DB_USER') ?: 'root';
        // $user = getenv('DB_USER') ?: 'mediastock';
        $pass = getenv('DB_PASSWORD') ?: '';

        // PDO utilise dsn (data source name) pour se connecter
        $dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        $options = [

          // pour gérer les erreurs, on choisit de lancer des exceptions
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

          // les résultats sont renvoyés en tableaux associatifs (clés = noms de colonnes) => pas besoin d'indices numériques
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        return new PDO($dsn, $user, $pass, $options);

    // }

   

?>