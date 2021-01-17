<?php

if (isset($_POST["submit"])) {

    // Récupération des données des champs de connection
    $username = $_POST["uid"];
    $pwd = $_POST["pwd"];

    require_once "dbh.inc.php";
    require_once 'functions.inc.php';

    // Les fonction utilisée ci-dessous sont dans "function.inc.php"

    // On vérifie que aucun champs ne soit vide
    if (emptyInputLogin($username, $pwd) === true) {
        header("location: https://shut-up-jr.herokuapp.com/login.php?error=emptyinput");
            exit();
    }

    // On connecte l'utilisateur
    // La fonction procède à la vérification de l'user et du mot de passe auprès de la base de données
    // Si les informations sont invalide l'utilisateur n'est pas connecté et est invité a saisir de nouveau ses identifiants 
    loginUser($conn, $username, $pwd);
} 
else {
	header("location: https://shut-up-jr.herokuapp.com/login.php");
    exit();
}
