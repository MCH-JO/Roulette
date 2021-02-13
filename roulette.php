<?php
	
	session_start();
	//var_dump($_SESSION);			//pour debugger
	//var_dump($_POST);				//"

	try {
		$bdd = new PDO('mysql:host=127.0.0.1; dbname=bdd_roulette; charset=utf8', 'p0401831', 'Lamborgh444');
	} catch (Exception $e) {
		die('Erreur: ' . $e->getMessage());
	}

	if (!isset($_SESSION['username']))
		header('Location: index.php');

	
	echo 'Bonjour ' . htmlspecialchars($_SESSION['username']);
	echo ', nous allons jouer à un jeu...<br>';
	$gain=0;															//gain eventuel en fonction de la selection du joueur
	$resultat='vide';												//phrase à afficher pour indiquer au joueur s'il a gagné ou pas
		
		$requete = 'select money from Player where username=:username';	
		$req = $bdd -> prepare($requete);
		$req -> execute(array('username' => $_SESSION['username']));
		$money = $req -> fetch();
		$req -> closeCursor();
	$porte_monnaie=$money['money'];						//on interroge la bdd afin de savoir de combien le joueur dispose
	$date=date("y-m-d-H-i-s");
	$nb=rand(1, 36);												//nb géneré de manière aleatoire; nb à trouver
	$parite='vide';
	if ($nb %2 == 0)
		$parite='pair';
	else $parite='impair';
			
	//Jeu de la roulette
	echo "$nb<br>";		//pr debugger
	echo "Choisisez une mise, puis un nombre ou la parité:<br>";
	echo 'Votre porte-monnaie: ' . $porte_monnaie . " €<br>";
	if (isset($_POST['envoyer'])) {
		if ($porte_monnaie >= $_POST['mise']) {		//on verifie le solde du joueur
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
				} elseif (isset($_POST['choix']) && isset($_POST['choix_p'])) {		//cas du mauvais utilisateur qui
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
			$porte_monnaie=$porte_monnaie - $_POST['mise'] + $gain;		//total du porte-monnaie du joueur
		} else 
			{
				echo 'Solde insuffisant!! ';
				unset($_POST['envoyer']);			//A TESTER..
			}	
	}
	
		$req1 = $bdd -> prepare('select id from Player where username=:username');		//on recupere l'id du joueur ds la bdd
		$req1 -> execute(array(
			'username' => $_SESSION['username']
			));
		$id = $req1 -> fetch();
		$req1 -> closeCursor();
		
		$req2 = $bdd -> prepare('update Player set money=:money where username=:username');		//on met à jour le porte-monnaie du joueur (table player)
		$req2-> execute(array('money' => $porte_monnaie,'username' => $_SESSION['username']));
		$req2-> closeCursor();
		
		$req3 = $bdd -> prepare('insert into Game (player, date, bet, profit) values (:player, :date, :bet, :profit)');		//on incremente la nvelle partie jouée ds la bdd
		$req3 -> execute(array(
			'player' => $id['id'],
			'date' => $date,
			'bet' => $_POST['mise'],
			'profit' => $gain
			));
		$req3 -> closeCursor();
		
		
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
		
