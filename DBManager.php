<?php
class DbManager {
	private $bdd;
	private $host; /*$_SERVER['SERVER_ADDR'];*/		//a voir!!
	private $dbname;			
	private $bddId;
	private $bddPwd;

	
	public function __construct($host, $dbname, $bddId, $bddPwd)
	{
		try
		{
			$this -> bdd = new PDO("mysql:host=$host; dbname=$dbname; charset=utf8", "$bddId", "$bddPwd");
		} 	catch (Exception $e) {
				die('Erreur: ' . $e->getMessage());
		}
		//$this->bdd->setAtribute()...
	}
	public function inscription($username, $password, $money)
	{
		$requete = 'insert into Player (username, password, money) values (?, ?, ?)';
		$req = $this -> bdd -> prepare($requete);
		//ou $requete=$bdd->prepare('insert into Player (username, password, money) values (:t_username, :t_password, 2000)');
		$req -> execute(array($username, $password, $money));
	}
	public function connection($username, $password)
	{
		$requete = 'select username, password from Player where username=:username';
		$req = $this -> bdd -> prepare($requete);
		$req -> execute(array(
						'username' => $username
					) );
		$resultat = $req -> fetch(PDO::FETCH_ASSOC);
		$req -> closeCursor();
		return $resultat;
	}
	public function getUsername($username)
	{
		$requete = 'select username from Player where username=:username';
		$req=$this -> bdd -> prepare($requete);
		$req -> execute(array('username' => $username));
		$resultat = $req -> fetch(PDO::FETCH_ASSOC);
		$req -> closeCursor();
		return $resultat;
	}
	public function updInscription($username, $password)
	{
		$requete = 'update Player set password=:password where username=:username';
		$req = $this -> bdd -> prepare($requete);
		$req -> execute(array('password' => $password, 'username' => $username));
		$req -> closeCursor();	
	}
	public function getId($username)
	{
		$req = $this -> bdd -> prepare('select id from Player where username=:username');		//on recupere l'id du joueur ds la bdd
		$req -> execute(array(
			'username'=>$username
			));
		$id = $req -> fetch(PDO::FETCH_ASSOC);
		$req -> closeCursor();
		return $id;
	}
	public function getMoney($username)		//au debut de la partie, on interroge la bdd afin de savoir de combien le joueur dispose
	{
		$requete = 'select money from Player where username=:username';	
		$req = $this -> bdd -> prepare($requete);
		$req -> execute(array('username' => $username));
		$money = $req -> fetch(PDO::FETCH_ASSOC);
		$req -> closeCursor();
		return $money;
	}
	public function setMoney($username, $money)
	{
		$req = $this -> bdd -> prepare('update Player set money=:money where username=:username');		//on met à jour le porte-monnaie du joueur (table player)
		$req-> execute(array('money' => $money,'username' => $username));
		$req-> closeCursor();
	}
	public function setNewGame($p, $d, $m, $g)
	{
		$req = $this -> bdd -> prepare('insert into Game (player, date, bet, profit) values (:player, :date, :bet, :profit)');		//on incremente la nvelle partie jouée ds la bdd
		$req -> execute(array(
			'player' => $p,
			'date' => $d,
			'bet' => $m,
			'profit' => $g
			));
		$req -> closeCursor();
	}
}

?>