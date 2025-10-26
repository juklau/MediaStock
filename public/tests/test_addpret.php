<?php
// URL de l'API à tester
$url = 'http://localhost/api/addpret.php'; 
// Données à envoyer
$data = [
    "item_id" => 66,
    "emprunteur_id" => 1,
    "preteur_id" => 1,
    "date_sortie" => "2025-10-26",
    "date_retour_prevue" => "2025-11-23",
    "note_debut" => "test",
    "note_fin" => "Bon"
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