<?php


// Fonction qui vérifie si les champs passé en paramètre sont vide (utilisé pour l'inscription)
function emptyInputSignup($name, $email, $username, $pwd, $pwdRepeat) {
	$result;
	if (empty($name) || empty($email) || empty($username) || empty($pwd) || empty($pwdRepeat)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Fonction qui vérifie si le pseudo a le bon format (utilisé pour l'inscription)
function invalidUid($username) {
	$result;
	if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Fonction qui vérifie si l'email a le bon format (utilisé pour l'inscription)
function invalidEmail($email) {
	$result;
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Fonction qui vérifie si deux mot de passe sont identique (utilisé pour l'inscription)
function pwdMatch($pwd, $pwdrepeat) {
	$result;
	if ($pwd !== $pwdrepeat) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Fonction qui vérifie si le pseudo ou l'email éxiste déja (utilisé pour l'inscription)
function uidExists($conn, $username, $email) {
    // Méthode qui va permètre de faire une requete dans la BD
    $query = new MongoDB\Driver\Query([]);

    // Cursor pour effectuer une requete 
    $cursor = $conn->executeQuery("ShutUp.formulaire", $query);

    // On parcours le résultat de la requete à la recherche du pseudo ou de l'email 
    foreach($cursor as $document) {
        if ($document->uid == $username || $document->email == $email) {
            return $document;
        }
    }
    return false;
}

// Insert un nouvel utilisateur dans la BD quand l'inscription est validé
function createUser($conn, $name, $email, $username, $pwd) {
	// Méthode qui va permètre d'insérer dans la BD 
	$bulk = new MongoDB\Driver\BulkWrite;
	
    // Déclaration des champs à insérer dans la BD
	$newUser = ['_id' => new MongoDB\BSON\ObjectId, 'name' => $name , 'email' => $email , 'uid' => $username , 'pwd' => $pwd];
	$newClient = $bulk->insert($newUser);

    // Exécute l'action pour insérer l'utilisateur
	$result = $conn->executeBulkWrite('ShutUp.formulaire', $bulk);

    // Redirige l'utilisateur vers une page de remerciement
	header("location: https://shut-up-jr.herokuapp.com/remerciement.php");
	exit();
}

// Fonction qui vérifie si un des champs lors de la connection est vide
function emptyInputLogin($username, $pwd) {
	$result;
	if (empty($username) || empty($pwd)) {
		$result = true;
	}
	else {
		$result = false;
	}
	return $result;
}

// Fonction qui permet de connecteé l'utilisateur au site
// La fonction va checker dans la BD si l'identifiant est connu et si le mot de passe est correct
function loginUser($conn, $username, $pwd) {
	$uidExists = uidExists($conn, $username,$username);

    // On vérifie si l'utilisateur éxiste
	if ($uidExists === false) {
		header("location: https://shut-up-jr.herokuapp.com/login.php?error=wronglogin");
		exit();
	}

    // On vérifie si le mot de passe correspond
	$pwdHashed = $uidExists->pwd;
	$checkPwd = $pwd === $pwdHashed;
	if ($checkPwd === false) {
		header("location: https://shut-up-jr.herokuapp.com/login.php?error=wronglogin");
		exit();
    }
    
    // Démarre la séssion
    session_start();
    
    // On récupère quelque information que l'on met dans un "dictionnaire" key/value (utile pour la page de profile)
	$_SESSION["userid"] = $uidExists->_id;
    $_SESSION["useruid"] = $uidExists->uid;
    $_SESSION["name"] = $uidExists->name;
    $_SESSION["email"] = $uidExists->email;

    // Redirige l'utilisateur
	header("location: https://shut-up-jr.herokuapp.com/index.php?error=none");
	exit();
}
