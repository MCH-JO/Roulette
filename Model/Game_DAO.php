<?php

require_once('Game_DTO.php');

class Game_DAO {
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
	public function getById($i)	//infos des parties jouées par l'id passé en paramètre et renvoie une instance DTO de ces parties
	{
		$requete= 'select * from Game where player=?';
		$req=$this -> bdd -> prepare($requete);
		$req ->execute([$i]);
		$resultat=$req -> fetch();
		$game=new Game_DTO($i, $resultat['date'], $resultat['bet'], $resultat['profit']);
		return $game;
	}

	public function setNewGame($p, $d, $m, $g)
	{
		$req = $this -> bdd -> prepare('insert into Game (player, date, bet, profit) values (:player, :date, :bet, :profit)');		//incremente la nvelle partie jouée ds la bdd
		$req -> execute(array(
			'player' => $p,
			'date' => $d,
			'bet' => $m,
			'profit' => $g
			));
		$req -> closeCursor();
	}
}
