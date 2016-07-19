<?php
namespace Library\Entities;

use \Library\Crypt ;

class User extends \Library\Entity
{
	protected $login,
			  $mdp,
			  $salt,
			  $solde,
			  $updated_at ;
	
	const LOGIN_INVALIDE = 1;
	const MDP_INVALIDE = 2;
	const SALT_INVALIDE = 3;
	const SOLDE_INVALIDE = 4;
	const UPDATED_AT_INVALIDE = 5 ;
	
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
		else 
		{
			$this->erreurs[] = self::SALT_INVALIDE;
		}
	}
	
	public function setSolde($montant)
	{
		if(!is_numeric($montant))
		{
			$this->erreurs[] = self::SOLDE_INVALIDE;
		}
	
		$this->solde = $montant;
	}
	
	public function setUpdated_at($date)
	{
		if (!empty($date) && !$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::UPDATED_AT_INVALIDE;
		}
		else
		{
			$this->updated_at = $date;
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
	
	public function solde()
	{
		return $this->solde;
	}
	
	public function updated_at()
	{
		return $this->updated_at;
	}
}