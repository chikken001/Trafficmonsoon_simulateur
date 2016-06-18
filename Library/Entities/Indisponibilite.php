<?php
namespace Library\Entities;

class Indisponibilite extends \Library\Entity
{
	protected $intermittent,
			  $datedebut,
			  $datefin;
	
	const INTERMITTENT_INVALIDE = 1;
	const DATEDEBUT_INVALIDE = 2;
	const DATEFIN_INVALIDE = 3;
  
  // SETTERS //
  
	public function setIntermittent($intermittent)
	{
		if (!is_int($intermittent) || empty($intermittent))
		{
			$this->erreurs[] = self::INTERMITTENT_INVALIDE;
		}
		
		$this->intermittent = $intermittent;
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
	
	public function intermittent()
	{
		return $this->intermittent;
	}
}