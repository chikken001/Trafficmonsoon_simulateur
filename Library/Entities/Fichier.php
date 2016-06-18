<?php
namespace Library\Entities;

class Fichier extends \Library\Entity
{
	protected $nom,
			  $operation,
			  $lien,
			  $date_ajout,
			  $date_modif;

	const NOM_INVALIDE = 1;
	const OPERATION_INVALIDE = 2;
	const LIEN_INVALIDE = 3;
	const DATE_AJOUT_INVALIDE = 4;
	const DATE_MODIF_INVALIDE = 5;

  
  // SETTERS //
  
	public function setNom($nom)
	{
		if (!$this->validator->is_Intitule($nom))
		{
			$this->erreurs[] = self::NOM_INVALIDE;
		}
		
		$this->nom = $nom;
	}
	
	public function setOperation($operation)
	{
		if (!$this->validator->is_Id($operation))
		{
			$this->erreurs[] = self::OPERATION_INVALIDE;
		}
		
		$this->operation = $operation;
	}
	
	public function setLien($lien)
	{
		if (!is_string($lien))
		{
			$this->erreurs[] = self::LIEN_INVALIDE;
		}
		
		$this->lien = $lien;
	}
	
	public function setDate_ajout($date)
	{
		if (!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATE_AJOUT_INVALIDE;
		}
		
		$this->date_ajout = $date;
	}
	
	public function setDate_modif($date)
	{
		if (!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATE_MODIF_INVALIDE;
		}
		
		$this->date_modif = $date;
	}
  
  // GETTERS //
  
	public function nom()
	{
		return $this->nom;
	}
	
	public function lien()
	{
		return $this->lien;
	}
	
	public function operation()
	{
		return $this->operation;
	}
	
	public function date_ajout()
	{
		return $this->date_ajout;
	}
	
	public function date_modif()
	{
		return $this->date_modif;
	}
}