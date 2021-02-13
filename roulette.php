<?php
	
	require_once('DBManager.php');
	$objetBdd= new DbManager('127.0.0.1', 'bdd_roulette', 'p0401831', 'Lamborgh444');
	
	session_start();
	//var_dump($_POST);  	//pour debugger

	if (!isset($_SESSION['username']))
		header('Location: index.php');

	
	echo 'Bonjour ' . htmlspecialchars($_SESSION['username']);
	echo ', nous allons jouer à un jeu...<br>';
	$gain=0;															//gain eventuel en fonction de la selection du joueur
	$resultat='vide';												//phrase à afficher pour indiquer au joueur s'il a gagné ou pas
	$porte_monnaie=$objetBdd->getMoney($_SESSION['username']);			//on interroge la bdd afin de savoir de combien le joueur dispose
	$date=date("y-m-d-H-i-s");
	$nb=rand(1, 36);												//nb géneré de manière aleatoire; nb à trouver
	$parite='vide';
	if ($nb %2 == 0)
		$parite='pair';
	else $parite='impair';
			
	//Jeu de la roulette
	echo "$nb<br>";		//pr debugger
	echo "Choisisez une mise, puis un nombre ou la parité:<br>";
	echo 'Votre porte-monnaie: ' . $porte_monnaie['money'] . " €<br>";
	if (isset($_POST['envoyer'])) {
		if ($porte_monnaie['money'] >= $_POST['mise']) {		//on verifie le solde du joueur
			if (isset($_POST['mise']) && ($_POST['mise'] >= 10)) {			//le joueur saisi une mise
				if (isset($_POST['choix']) && (!isset($_POST['choix_p']))) {
					if ($_POST['choix'] <= 36 && $_POST['choix'] >= 1) {
						if ($_POST['choix']==$_POST['NB']) {
							$resultat='Jackpot!! Vous avez choisi le numero ' . $_POST['choix'] . ' et c est bien lui qui est sorti';
							$gain= $_POST['mise'] * 35;
						} else $resultat='Perdu, vous avez choisi le numero ' . $_POST['choix'] . " alors que c est le numero $nb qui est sorti";
					} else $resultat='La valeur choisie n est pas ds la tolerance, choisissez un nombre entre 1 et 36';
				} elseif (isset($_POST['choix_p']) && (!isset($_POST['nb'])) && $_POST['choix']==0) { 	//le joueur saisit une parité
					if ($_POST['choix_p']==$_POST['PARITE']) {
						$gain = $_POST['mise'] * 2;
						$resultat="Gagné!! $nb est $parite!!";
					} else $resultat="Perdu: parite incorrecte, ben oui $nb est $parite";
				} elseif (isset($_POST['choix']) && isset($_POST['choix_p'])) {		//cas du mauvais utilisateur qui saisi
						unset($_POST['choix_p']);									//saisit une valeur et une parité
						if ($_POST['choix']==$_POST['NB']) {
							$resultat='Jackpot!! Vous avez choisi le numero ' . $_POST['choix'] . ' et c est bien lui qui est sorti!!';
							$gain = $_POST['mise'] * 35;
						} else $resultat='Perdu, vous avez choisi le numero ' . $_POST['choix'] . " alors que c est le numero $nb qui est sorti";
					}
			} else
				{
					$resultat='Misez! (ou somme insuffisante)';
					$gain=0;
				}
			$porte_monnaie['money']=$porte_monnaie['money'] - $_POST['mise'] + $gain;	//total du porte-monnaie du joueur
		} else 
			{
				echo 'Solde insuffisant!! ';
				unset($_POST['envoyer']);			//A TESTER..
			}	
	}
	
	$id=$objetBdd->getId($_SESSION['username']);							//on recupere l'id du joueur
	$objetBdd->setMoney($_SESSION['username'], $porte_monnaie['money']);		//on met à jour le porte-monnaie du joueur
	$objetBdd->setNewGame($id['id'], $date, $_POST['mise'], $gain);			//on incremente chaque partie dans la bdd
		
		
/*htmlspecialschars($_POST['']);
strip_tags; pr supprimer les balises html*/
?>
		
<!DOCTYPE html>
<html>
	<head>
		<title>Roulette</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="roulette.css" type="text/css">
	</head>
	<body></h1>
		<h1>Jeu de la roulette</h1>
		<form method="post" action="roulette.php">
			Mise<input type="number" name="mise" value="10" step="5" min="10">
			Choix (entre 1 et 36)<input type="number" name="choix" value="0" min="0" max="36">
			<input type="hidden" name="NB" value="<?php echo $nb;?>">
			<label>Pair<input type="radio" name="choix_p" value="pair"></label>
			<label>Impair<input type="radio" name="choix_p" value="impair"></label>
			<input type="hidden" name="PARITE" value="<?php echo $parite;?>">
			<input type="submit" name="envoyer">
			<input type="reset" name="reinitialiser_mise">
		</form>
		<p class="resultat"><?php if ($resultat!='vide') echo $resultat ?><p>		<!--on affiche victoire ou pas (perdu, gagné!!, ...)-->
		<p><?php echo "Vos gains: $gain €" ?><p>		<!--on affiche les gains-->
		<div>
			<a href='roulette.php'>Réinitialiser</a><br>
			<a href="index.php?Deco">Se deconnecter, retour à la page d'accueil</a><br>	
		</div>		
	</body>
</html>
		
