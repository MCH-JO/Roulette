<?php

	require_once('DBManager.php');														//on inclut le fichier
	$objetBdd=new DbManager('127.0.0.1', 'bdd_roulette', 'p0401831', 'Lamborgh444');
	
	session_start();
	//var_dump($_SESSION);			//pour debugger
	//var_dump($_POST);				//"

	if (isset($_GET['Deco']))
		unset($_SESSION['username']);
	
	$resultat=$objetBdd -> getUsername($_POST['username']);		//on recupere ds la bdd le login du joueur
		
	if (isset($_POST['creer'])) {
		if(isset($_POST['username']) && $_POST['username'] != '') {
			if(isset($_POST['password']) && isset($_POST['password2']) && $_POST['password'] != '' && $_POST['password2'] != '') {
				if ($_POST['password'] == $_POST['password2']) {
					if ((strcmp($_POST['username'], $resultat)==0) || (strcmp($_SESSION['username'], $resultat)==0)) {		//on regarde si le nom proposé existe deja ds la bdd
						$objetBdd -> updInscription($_POST['username'], $_POST['password']);								//si oui (ou si le joueur est deja connecté), on MàJ le mdp
						$_SESSION['username'] = $_POST['username'];
					} else {
						$objetBdd -> inscription($_POST['username'], $_POST['password'], 2000);								//sinon on crée un nouveau compte
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



