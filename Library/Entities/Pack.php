<?php
namespace Library\Entities;

use \Library\Crypt ;

class Pack extends \Library\Entity
{
	protected $id_user,
			  $date_achat;
	
	const ID_USER_INVALIDE = 1;
	const DATE_ACHAT_INVALIDE = 2;
  
  
  // SETTERS //
	
	public function setId_user($id)
	{
		if (!$this->validator->is_Id($id))
		{
			$this->erreurs[] = self::ID_USER_INVALIDE;
		}
		
		$this->id_user = $id;
	}
	
	public function setDate_achat($date_achat)
	{
		if (!$this->validator->is_Date($date_achat, 'datetime'))
		{
			$this->erreurs[] = self::DATE_ACHAT_INVALIDE;
		}
		else
		{
			$this->date_achat = $date_achat;
		}
	}
  
  // GETTERS //
	
	public function id_user()
	{
		return $this->id_user;
	}
	
	public function date_achat()
	{
		$date = new \DateTime($this->date_achat) ;
		return $date->format('d/m/Y H:i:s');
	}
}