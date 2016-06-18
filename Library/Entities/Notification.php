<?php
namespace Library\Entities;

class Notification extends \Library\Entity
{
	protected $role,
			  $technicien,
			  $maj,
			  $statut;
	
	const ROLE_INVALIDE = 1;
	const TECHNICIEN_INVALIDE = 2;
	const STATUT_INVALIDE = 3;
	const MAJ_INVALIDE = 4;
	
	const TECHNICIEN_NULL = 5;
	
	public function isValid()
	{
		if((empty($this->technicien) || $this->technicien === null))
		{
			$this->erreurs[] = self::TECHNICIEN_NULL;
		}
		
		return (count($this->erreurs) === 0);
	}
  
  // SETTERS //
	
	public function setTechnicien($technicien = false)
	{
		if (!$this->validator->is_Id($technicien) && $technicien != false)
		{
			$this->erreurs[] = self::TECHNICIEN_INVALIDE;
		}
		
		$this->technicien = $technicien;
	}
	
	public function setRole($role)
	{
		if (!$this->validator->is_Id($role))
		{
			$this->erreurs[] = self::ROLE_INVALIDE;
		}
		
		$this->role = $role;
	}
	
	public function setStatut($statut = 0)
	{
		if (!is_int($statut) || $statut > 255)
		{
			$this->erreurs[] = self::STATUT_INVALIDE;
		}
		
		$this->statut = $statut;
	}
	
	public function setMaj($date)
	{
		if(!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::MAJ_INVALIDE;
		}
		
		$this->maj = $date ;
	}
  
  // GETTERS //
  
	public function statut()
	{
		return $this->statut;
	}
	
	public function role()
	{
		return $this->role;
	}
	
	public function technicien()
	{
		return $this->technicien;
	}
	
	public function maj()
	{
		return $this->maj;
	}
}