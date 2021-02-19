<?php


class Player_DTO {
	private $id ;
	private $username;
	private $password;
	private $money;

	public function __construct($i, $u, $p, $m)
	{
		$this->id=$i;
		$this->username=$u;
		$this->password=$p;
		$this->money=$m;
	}

	//getters
	public function getId()
	{
		return $this->id;
	}
	public function getUsername()
	{
		return $this->username;
	}
	public function getPassword()
	{
		return $this->password;
	}
	public function getMoney()
	{
		return $this->money;
	}
}
