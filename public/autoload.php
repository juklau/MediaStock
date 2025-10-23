<?php

    /**
    * Chargeur automatique pour la couche d'accès aux données MediaStock
    *
    * Ce fichier fournit un chargeur automatique simple pour la couche d'accès aux données MediaStock.
    * Inclure ce fichier dans les scripts PHP pour charger automatiquement les classes requises.
    */


    // Définir les mappings de namespace vers les répertoires afin d'adapter à mes chemins!!!
    $prefixes = [
        // __DIR__ = le dossier du fichier actuel!!
        // en PHP '\\' === '\' dans une chaîne!!
        // Si une classe commence par le namespace Models\, alors cherche son fichier dans src/models/
        'Models\\' => __DIR__ . '/../src/models/',
        'Config\\' => __DIR__ . '/../config/',
        // 'Controllers\\' => __DIR__ . '/../src/controllers/',
        // 'Controllers\\' => __DIR__ . '/../src/views/',
    ];

    // Enregistrer le chargeur automatique => à appeler par PHP pour trouver la classe
    // $class => p.ex. Models\Item
    spl_autoload_register(function ($class) use ($prefixes) {


        // rechercher le bon mapping 
        foreach ($prefixes as $prefix => $baseDir) {

            // Vérifie si la classe commence par le préfixe
            if (strpos($class, $prefix) === 0) { // p.ex si Models\Item commence par Models\

                // Supprime le préfixe pour obtenir le nom relatif => Models\Item ==>Item
                $relativeClass = substr($class, strlen($prefix));

                // Construit le chemin du fichier => /../src/models/Item.php
                // str_replace() transforme les \ en / (ou \ sur Windows)
                $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

                // Inclut le fichier s’il existe
                if (file_exists($file)) {
                    require $file;
                    return true;
                }
            }
        }

        return false;
    });

    // fonctionnement du code: $item = new Models\Item(); =>dans populate_database.php
            // PHP appelle la fonction avec $class = 'Models\Item'
            // Le code cherche dans ../src/models/Item.php
            // Si le fichier existe, il est inclus automatiquement


    // Initialisation de la connexion de BDD
    // require_once 'config/Database.php'; => a profé!!
    require_once __DIR__ . '/../config/Database.php'; //ez az enyem =>elvileg müködik!!

    // retourne l'instance de connexion de la BDD
    function getDatabase() {
        return Config\Database::getInstance()->getConnection();
    }

    // une fonction d'aide pour créer une instance de model
    function getModel($modelName) {
        $className = 'Models\\' . $modelName;
        return new $className();
    }

?>
