<?php

require_once('Player_DTO.php');


class Player_DAO {
	private $bdd;

	public function __construct($host, $dbname, $bddId, $bddPwd)
	{
		try {
			$this->bdd=new PDO("mysql:host=$host; dbname=$dbname; charset=utf8", "$bddId", "$bddPwd");
			$this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch(Exception $e) {
			die('Erreur: ' . $e->getMessage());
		}
	}

	public function getByUsername($u)  			//obtient infos du joueur par son username et on renvoie une instance DTO de ce joueur
	{
		$requete='select * from Player where username=?';
		$req= $this -> bdd -> prepare($requete);
		$req -> execute([$u]);
		$resultat = $req -> fetch();
		$player = new Player_DTO($resultat['id'], $u, $resultat['password'], $resultat['money']);
		return $player;
	}

	public function inscription($username, $password, $money)	//ajoute un nouveau joueur ds la bdd
	{
		$requete = 'insert into Player (id, username, password, money) values (?, ?, ?)';
		$req = $this ->bdd-> prepare($requete);
		//ou $requete=$bdd->prepare('insert into Player (username, password, money) values (:t_username, :t_password, 2000)');
		$req -> execute(array($id, $username, $password, $money));
	}
	public function connection($username)
	{
		$requete='select * from Player where username=:username';
		$req = $this->bdd->prepare($requete);
		$req->execute(array('username' => $username));
		$resultat=$req->fetch();
		$req -> closeCursor();
		return $resultat;
	}
	public function getUsername($username)		//recherche le pseudo ds la bdd (afin de savoir s'il existe deja par exemple)
	{
		$requete = 'select username from Player where username=:username';
		$req = $this -> bdd -> prepare($requete);
		$req -> execute(array('username' => $username));
		$resultat = $req -> fetch();
		$req -> closeCursor();
		return $resultat;
	}
	public function updInscription($username, $password)		//met à jour le mot de passe d'un joueur déjà enregistré
	{
		$requete = 'update Player set password=:password where username=:username';
		$req = $this -> bdd -> prepare($requete);
		$req -> execute(array('password' => $password, 'username' => $username));
		$req -> closeCursor();
	}
	public function getId($username)			//retourne l'id (username passé en paramètre)
	{
		$req = $this -> bdd -> prepare('select id from Player where username=:username');
		$req -> execute(array(
			'username'=>$username
			));
		$id = $req -> fetch();
		$req -> closeCursor();
		return $id;
	}
	public function getMoney($username)			//retourne le montant dont le joueur dispose
	{
		$requete = 'select money from Player where username=:username';
		$req = $this -> bdd -> prepare($requete);
		$req -> execute(array('username' => $username));
		$money = $req -> fetch();
		$req -> closeCursor();
		return $money;
	}
	public function setMoney($username, $money)//met à jour le porte-monnaie du joueur
	{
		$req = $this -> bdd -> prepare('update Player set money=:money where username=:username');
		$req-> execute(array('money' => $money,'username' => $username));
		$req-> closeCursor();
	}
}
