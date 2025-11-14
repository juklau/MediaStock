<?php
// URL de l'API à tester
$url = 'http://localhost/api/addemprunteur.php'; 
// Données à envoyer
$data = [
    'emprunteur_nom' => 'Elemer', 
    'emprunteur_prenom' => 'Klaudia',
    'role' => 'intervenant',
    'formation_id' => null
];



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