<?php
	session_start();
	require_once('../Model/Player_DAO.php');
	require_once('../Model/Game_DAO.php');


	//var_dump($_SESSION);		//pour debugger
	//var_dump($_POST);			// "

	//instanciation objets DAO (bdd)
	$dao_p=new Player_DAO('127.0.0.1', 'bdd_roulette', 'p0401831', 'eNcgNSU0dRXxNZWn');
	$dao_g=new Game_DAO('127.0.0.1', 'bdd_roulette', 'p0401831', 'eNcgNSU0dRXxNZWn');

	//Utilise objet DTO, évite d'interroger la bdd pr retrouver les caracteritiques du joueur
	$player=$dao_p->getByUsername($_SESSION['username']);						//joueur déjà connecté et donc enregistré!!
	$id=$player->getId();
	$username=$player->getUsername();
	$password=$player->getpassword();
	$porte_monnaie=$player->getMoney();

	//declaration des variables
	$gain=0;					//gain eventuel en fonction de la selection du joueur
	$message='vide';			//affichage pour page connection
	$message1='vide';			//affihcage pour page d'inscription
	$message2='vide';			//affichage pour le jeu de la roulette
	$module='vide';				//servira lors de la redirection
	$date=date("y-m-d-H-i-s");	//GDH
	$nb=rand(1, 36);			//nb géneré de manière aleatoire; nb à trouver
	$parite='vide';				//parité du nb à trouver
	if ($nb %2 == 0)
		$parite='pair';
	else $parite='impair';

	//remplace les liens et la redirection
	if(isset($_POST['deconnection'])) {
		unset($_SESSION['username']);
		$module='../View/connection.php';
	}
	if(isset($_POST['reinitialiser_mise'])){
		$module='../View/roulette.php';
	}
	if(isset($_SESSION['username'])) {
		$module='../View/roulette.php';
		if(isset($_POST['inscription']))
			$module='../View/inscription.php';
	} else $module='../View/connection.php';
	//bouton reset
	if(isset($_POST['nouvelle_partie'])) {
		$module='../View/roulette.php';
		$gain=0;
	}

	//traitement formulaire de connection
	$reponse=$dao_p->connection($_POST['username']);		//on recupere login + mdp ds la base afin de comparer avec ce qu'a saisi l'utilisateur
	if (isset($_POST['connecter'])) {
		if(isset($_POST['username']) && $_POST['username'] != '' && isset($_POST['password']) && $_POST['password'] != '') {
			if ($_POST['username'] ==  $reponse['username'] && $_POST['password'] == $reponse['password']) {
				$_SESSION['username'] = $_POST['username'];
				$module='../View/roulette.php';
			} else {
				$message='Erreur de saisie de l\'identifiant ou du mot de passe';
			}
		} else { $message='Erreur, champs vides';}
	}

	//traitement formulaire d'inscription
	if (isset($_POST['creer'])) {
		$reponse1=$dao_p -> getUsername($_POST['username']);					//on recupere ds la bdd le login du joueur
		if(isset($_POST['username']) && $_POST['username'] != '') {
			if(isset($_POST['password']) && isset($_POST['password2']) && $_POST['password'] != '' && $_POST['password2'] != '') {
				if ($_POST['password'] == $_POST['password2']) {
					if ((strcmp($_POST['username'], $reponse1['username'])==0) || (strcmp($_SESSION['username'], $reponse1['username'])==0)) {	//regarde si le nom proposé existe deja ds la bdd...
						$dao_p -> updInscription($_POST['username'], $_POST['password']);													//...si oui (ou si deja connecté) => MàJ du mdp
						$_SESSION['username'] = $_POST['username'];
					} else {
						$dao_p -> inscription($_POST['username'], $_POST['password'], 2000);												//...sinon => nouveau compte
						$_SESSION['username'] = $_POST['username'];
					}
					$message1='Compte enregistré ou mis à jour';
					$module='../View/roulette.php';
				} else $message1='Erreur de saisie: les mots de passe sont differents';
			} else $message1='Erreur, champ(s) mot de passe vide(s)';
		} else $message1='Saisir un login';
	}

	//traitement formulaire jeu de la roulette
	if (isset($_POST['envoyer'])) {
		if ($porte_monnaie >= $_POST['mise']) {									//verifie le solde du joueur
			if (isset($_POST['mise']) && ($_POST['mise'] >= 10)) {				//le joueur saisi une mise
				if (isset($_POST['choix']) && (!isset($_POST['choix_p']))) {
					if ($_POST['choix'] <= 36 && $_POST['choix'] >= 1) {
						if ($_POST['choix']==$_POST['NB']) {
							$message2='Jackpot!! Vous avez choisi le numero ' . $_POST['choix'] . ' et c est bien lui qui est sorti';
							$gain= $_POST['mise'] * 35;
						} else $message2='Perdu, vous avez choisi le numero ' . $_POST['choix'] . ' alors que c est le numero ' . $nb . ' qui est sorti';
					} else $message2='La valeur choisie n est pas ds la tolerance, choisissez un nombre entre 1 et 36';
				} elseif (isset($_POST['choix_p']) && $_POST['choix']==0) { 			//le joueur saisit une parité
					if ($_POST['choix_p']==$_POST['PARITE']) {
						$gain = $_POST['mise'] * 2;
						$message2='Gagné!! ' . $nb . ' est ' . $parite;
					} else $message2='Perdu: parite incorrecte, ben oui ' . $nb . ' est ' . $parite;
				} elseif ($_POST['choix']!=0 && isset($_POST['choix_p'])) {				//cas du mauvais utilisateur qui...
						unset($_POST['choix_p']);										//...saisit une valeur et une parité => parité non prise en compte
						if ($_POST['choix']==$_POST['NB']) {
							$message2='Jackpot!! Vous avez choisi le numero ' . $_POST['choix'] . ' et c est bien lui qui est sorti!!';
							$gain = $_POST['mise'] * 35;
						} else $message2='Perdu, vous avez choisi le numero ' . $_POST['choix'] . ' alors que c est le numero ' . $nb . ' qui est sorti';
					}
			} else
				{
					$message2='Misez! (ou somme insuffisante)';
					$gain=0;
				}
			$porte_monnaie=$porte_monnaie - $_POST['mise'] + $gain;						//porte-monnaie du joueur  + gains - mise
			$dao_g->setNewGame($id, $date, $_POST['mise'], $gain);						//partie incrémentée dans la bdd
			$dao_p->setMoney($username, $porte_monnaie);								//porte-monnaie du joueur mis à jour
		} else
			{
				$message2= 'Solde insuffisant!! ';
				$module='../View/roulette.php';											//cliquer sur "envoyer" renverra sur la page d'accueil du jeu (=>partie non prise en compte)
			}
	}


	//Vues
	include('../View/head.php');
	include("$module");				//peut inclure page de connection ou la page d'inscription ou la page de jeu
	include('../View/footer.php');
