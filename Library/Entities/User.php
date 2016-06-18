<?php
namespace Library\Entities;

use \Library\Crypt ;

class User extends \Library\Entity
{
	protected $login,
			  $mdp,
			  $salt;
	
	const NOM_INVALIDE = 1;
	const MDP_INVALIDE = 2;
	const SALT_INVALIDE = 3;
	
	const PASSWORD_VERIFICATION = 101;
  
  
  // SETTERS //
	
	public function setLogin($login)
	{
		if (!$this->validator->is_Login($login))
		{
			$this->erreurs[] = self::LOGIN_INVALIDE;
		}
		
		$this->login = $login;
	}
	
	public function setMdp($password)
	{
		if (!$this->validator->is_Password($password))
		{
			$this->erreurs[] = self::MDP_INVALIDE;
		}
		else
		{
			$this->mdp = $password;
		}
	}
	
	public function setSalt($salt)
	{
		if(strlen($salt) == 10)
		{
			$this->salt = (string)$salt ;
		}
	}
  
  // GETTERS //
	
	public function login()
	{
		return $this->login;
	}
	
	public function salt()
	{
		return $this->salt;
	}
	
	public function mdp()
	{
		return $this->mdp;
	}
}