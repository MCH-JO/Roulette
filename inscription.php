<?php

	session_start();
	//var_dump($_SESSION);			//pour debugger
	//var_dump($_POST);				//"

	try {
		$bdd = new PDO('mysql:host=127.0.0.1; dbname=bdd_roulette; charset=utf8', 'p0401831', 'Lamborgh444');
	} catch (Exception $e) {
		die('Erreur: ' . $e->getMessage());
	}

	if (isset($_GET['Deco']))
		unset($_SESSION['username']);
	

	$requete = 'select username from Player where username=:username';	
	$req=$bdd -> prepare($requete);
	$req -> execute(array('username' => $username));
	$resultat = $req -> fetch();
	$req -> closeCursor();


		
	if (isset($_POST['creer'])) {
		if(isset($_POST['username']) && $_POST['username'] != '') {
			if(isset($_POST['password']) && isset($_POST['password2']) && $_POST['password'] != '' && $_POST['password2'] != '') {
				if ($_POST['password'] == $_POST['password2']) {
					if ((strcmp($_POST['username'], $resultat['username'])==0) || (strcmp($_SESSION['username'], $resultat['username'])==0)) {		//on regarde si le nom proposé existe deja ds la bdd
						$requete1 = 'update Player set password=:password where username=:username';												//si oui (ou si le joueur est deja connecté), on MàJ le mdp
						$req1 = $bdd -> prepare($requete1);
						$req1 -> execute(array('password' => $_POST['password'], 'username' => $_POST['username']));
						$req1 -> closeCursor();								
						$_SESSION['username'] = $_POST['username'];
					} else {
						$requete2 = 'insert into Player (username, password, money) values (?, ?, ?)';
						$req2 = $bdd -> prepare($requete2);
						//ou $requete=$bdd->prepare('insert into Player (username, password, money) values (:t_username, :t_password, 2000)');
						$req2 -> execute(array($_POST['username'], $_POST['password'], 2000));														//sinon on crée un nouveau compte
						$_SESSION['username'] = $_POST['username'];
					}
					echo "Compte enregistré ou mis à jour<br>";
					echo "<a href='roulette.php'>Jouer</a>"; //ou header('Location: roulette.php');
				} else echo "Erreur de saisie: les mots de passe sont differents<br>";
			} else echo "Erreur, champ(s) mot de passe vide(s)<br>";
		} else echo 'Saisir un login';
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Inscription</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="roulette.css" type="text/css">
	</head>
	<body>
		<h1>Bienvenue sur le jeu de la roulette</h1>
		<h2>Inscription</h2>
		<p>Choisissez un login et un mot de passe</p>
		<form method="post" action="inscription.php">
			<input type="text" name="username" placeholder="Identifiant">
			<input type="password" name="password" placeholder="Mot de Passe">
			<input type="password" name="password2" placeholder="Mot de Passe">
			<input type="submit" name="creer">
			<input type="reset" name="effacer">
		</form>
		<a href="index.php?Deco">Se deconnecter, retour à la page d'accueil</a><br>	
	</body>
</html>



