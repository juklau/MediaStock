<?php
    $url = 'http://localhost/api/updateitem.php';

    $data = [
        'id' => 90,
        'qr_code' => 94,
        
    ];


    $options = [
    'http' => [
        'method'  => 'POST',
        'header'  =>
            "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n",
        'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
        'ignore_errors' => true, // pour récupérer le corps même en cas de code HTTP d’erreur
    ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    // echo $result;

    if ($result === false) {
        echo "Requête échouée.\n";
    } else {
        echo $result;
    }

?>
