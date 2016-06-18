<?php
namespace Library\Entities;

class Indisponibilite_Temporaire extends \Library\Entity
{
	protected $intermittent_temporaire,
			  $datedebut,
			  $datefin;
	
	const INTERMITTENT_TEMPORAIRE_INVALIDE = 1;
	const DATEDEBUT_INVALIDE = 2;
	const DATEFIN_INVALIDE = 3;
  
  // SETTERS //
  
	public function setIntermittent_temporaire($intermittent)
	{
		if (!is_int($intermittent) || empty($intermittent))
		{
			$this->erreurs[] = self::INTERMITTENT_TEMPORAIRE_INVALIDE;
		}
		
		$this->intermittent_temporaire = $intermittent;
	}
	
	public function setDatedebut($date)
	{
		if(!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATEDEBUT_INVALIDE;
		}
		
		$this->datedebut = $date;
	}
	
	public function setDatefin($date)
	{
		if(!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATEFIN_INVALIDE;
		}
		
		$this->datefin = $date;
	}
	
  
  // GETTERS //
  
	public function datedebut()
	{
		return $this->datedebut;
	}
	
	public function datefin()
	{
		return $this->datefin;
	}
	
	public function intermittent_temporaire()
	{
		return $this->intermittent_temporaire;
	}
}