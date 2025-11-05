<?php
session_start();
$_SESSION['username'] = null;
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header('Location: acceuil.html');
    exit();
}