<?php
    $url = 'http://localhost/api/updateitem.php';

    $data = [
        'id' => 101,
        'qr_code' => 101,
        'nom' => 'Micro cravate',
        'etat' => 'bon',
        'categorie_id' => 2
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
    echo $result;

?>
