<?php
    require_once __DIR__ . '/../auth_check.php';

    header('Content-Type: application/json');

    $response = [
        'isAuthenticated' => isset($_SESSION['username']),
        'redirectUrl' => isset($_SESSION['username']) ? null : 'acceuil.html'
    ];

    echo json_encode($response);
?>