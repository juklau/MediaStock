<?php
    $url = 'http://localhost/api/cloturepret.php';

    $data = [
        'id' => 28,
        'note_fin' => 'Elle était bien rendu à ce jour, à minuit'
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
