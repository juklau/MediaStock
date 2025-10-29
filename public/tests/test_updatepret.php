<?php
    $url = 'http://localhost/api/updatepret.php';

    $data = [
        'id' => 17,
        'date_sortie' => '2025-10-01',
        'date_retour_prevue' => '2025-11-15',
        'note_debut' => 'Bon état, testé avant sortie, après minuit'
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json",
            'method'  => 'POST',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        echo "Erreur lors de la requête vers l'API.";
    } else {
        echo $result;
    }
?>