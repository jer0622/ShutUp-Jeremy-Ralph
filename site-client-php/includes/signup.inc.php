<?php


if (isset($_POST["submit"])) {

    // Récupération des données du formulaire
    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["uid"];
    $pwd = $_POST["pwd"];
    $pwdRepeat = $_POST["pwdrepeat"];

    require_once "dbh.inc.php";
    require_once 'functions.inc.php';

    // Les fonction utilisée ci-dessous sont dans "function.inc.php"

    // On vérifie que aucun champs ne soit vide
    if (emptyInputSignup($name, $email, $username, $pwd, $pwdRepeat) !== false) {
        header("location: https://shut-up-jr.herokuapp.com/signup.php?error=emptyinput");
        exit();
    }
    
    // On vérifie le bon format du pseudo
    if (invalidUid($uid) !== false) {
        header("location: https://shut-up-jr.herokuapp.com/signup.php?error=invaliduid");
        exit();
    }

    // On vérifie le bon format de l'email
    if (invalidEmail($email) !== false) {
        header("location: https://shut-up-jr.herokuapp.com/signup.php?error=invalidemail");
        exit();
    }

    // On vérifie que les deux mots de passe sont identique
    if (pwdMatch($pwd, $pwdRepeat) !== false) {
        header("location: https://shut-up-jr.herokuapp.com/signup.php?error=passwordsdontmatch");
        exit();
    }

    // On vérifie si le pseudo ou l'email éxiste déja dans la BD
    if (uidExists($conn, $username, $email) !== false) {
        header("location: https://shut-up-jr.herokuapp.com/signup.php?error=usernametaken");
        exit();
    }

    // Tout les cas de test ci-dessus sont validé alors insertion du nouvel utilisateur
    createUser($conn, $name, $email, $username, $pwd);

} 
else {
	header("location: https://shut-up-jr.herokuapp.com/signup.php");
    exit();
}
