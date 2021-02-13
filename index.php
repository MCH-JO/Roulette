<?php	
	
	require_once('DBManager.php');	
	$objetBdd= new DbManager('127.0.0.1', 'bdd_roulette', 'p0401831', 'Lamborgh444');
	
	session_start();
	//var_dump($_SESSION);		//pour debugger
	//var_dump($_POST);			// "
	
	if (isset($_SESSION['username'])) {
		header('Location: roulette.php');
	}
	
	if (isset($_GET['Deco'])) {
		unset($_SESSION['username']);
	}
	
	$resultat=$objetBdd -> connection($_POST['username'], $_POST['password']);
	
	if (isset($_POST['connecter'])) {
		if(isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['password']) && $_POST['password'] != '') {
			if ($_POST['username'] ==  $resultat['username'] && $_POST['password'] == $resultat['password']) {
				$_SESSION['username'] = $_POST['username'];
				//echo "Ca va pour cette fois...<br><br>";
				header('Location: roulette.php');		//ou echo "<a href='roulette.php'>Jouer</a>";	//le lien apparait si login+mdp ok
			} else { echo "Erreur de saisie de l identifiant ou du mot de passe<br>";} 
		} else { echo "Erreur, champs vides<br>";}
	}
	
?>
	
	
<!DOCTYPE html>
<html>
	<head>
		<title>Connexion</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="roulette.css" type="text/css">
	</head>
	<body>
	<h1>Bienvenue sur le jeu de la roulette</h1>
		<h2>Connexion</h2>
		<form method="post" action="index.php">
			<input type="text" name="username" placeholder="Identifiant">
			<input type="password" name="password" placeholder="Mot de Passe">
			<input type="submit" name="connecter">
			<input type="reset" name="effacer">
		</form>
		<a href="inscription.php">Inscription</a>
	</body>
</html>

