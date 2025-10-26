<?php
// URL de l'API à tester
$url = 'http://localhost/api/addadmin.php'; // adapte le chemin si nécessaire

// Données à envoyer
$data = [
    'login' => 'test' . rand(1000, 9999), // login unique pour éviter les doublons
    'mot_de_passe_hash' =>'test'  // hash sécurisé
];

// test2360
// test

// Options de la requête HTTP
$options = [
    'http' => [
        'header'  => "Content-Type: application/json",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];

// Créer le contexte et envoyer la requête
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

// Afficher le résultat brut
echo "Réponse brute :\n";
echo $result . "\n";

// Décoder et afficher en tableau
$response = json_decode($result, true);
echo "\nRéponse décodée :\n";
print_r($response);