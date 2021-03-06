<?php
namespace Library\Entities;

use \Library\Crypt ;

class Pack extends \Library\Entity
{
	protected $id_user,
			  $date_achat,
			  $date,
			  $montant;
	
	const ID_USER_INVALIDE = 1;
	const DATE_ACHAT_INVALIDE = 2;
	const DATE_INVALIDE = 3;
	const MONTANT_INVALIDE = 4;
  
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
		if (!empty($date_achat) && !$this->validator->is_Date($date_achat, 'datetime'))
		{
			$this->erreurs[] = self::DATE_ACHAT_INVALIDE;
		}
		
		if(empty($date_achat)) $date_achat = null;
		$this->date_achat = $date_achat;
		
	}
	
	public function setDate($date)
	{
		if(!empty($date) && !$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATE_INVALIDE;
		}
		else
		{
			if(empty($date)) $date = date('d/m/Y H:i:s');
			$this->date = $date;
		}
	}
	
	public function setMontant($montant)
	{
		if(!empty($montant) && !is_numeric($montant))
		{
			$this->erreurs[] = self::MONTANT_INVALIDE;
		}
		
		if(empty($montant)) $montant = 0 ;
		$this->montant = $montant;
	}
  
  // GETTERS //
	
	public function id_user()
	{
		return $this->id_user;
	}
	
	public function date_achat()
	{
		return $this->date_achat ;
	}
	
	public function date()
	{
		return $this->date ;
	}
	
	public function montant()
	{
		return $this->montant ;
	}
}