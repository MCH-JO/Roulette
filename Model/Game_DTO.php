<?php

//inutile dans notre cas d'utlisation
class Game_DTO {
	private $player;
	private $date;
	private $bet;
	private $profit;

	public function __construct($i, $d, $b, $g)
	{
		$this->player=$i;
		$this->date=$d;
		$this->bet=$b;
		$this->profit=$g;
	}
	//getters
	public function getPlayerId()
	{
		return $this->player;
	}
	public function getMise()
	{
		return $this->bet;
	}
	public function getGain()
	{
		return $this->profit;
	}

}
