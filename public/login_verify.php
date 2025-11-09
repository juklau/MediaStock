<?php
session_start();

// durée maximale d'inactivité en secondes (5min => 300s)
$session_timeout = 300;

// vérification si user est connecté
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header('Location: acceuil.html');
    exit();
}

// vérification si la session a expiré
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout){

    // déstruction de la session et redirection vers acceuil.html
    session_unset();
    session_destroy();
    header("Location:index.php");
    exit;
}

// mise à jour de l'heure du dernière activité
$_SESSION['last_activity'] = time();
?>