<?php
namespace Library\Entities;

class Planning extends \Library\Entity
{
	protected $intitule,
			  $date,
			  $operation ;
	
	const INTITULE_INVALIDE = 1;
	const DATE_INVALIDE = 2;
	const OPERATION_INVALIDE = 3 ;
  
  // SETTERS //
  
	public function setIntitule($intitule)
	{
		if (!$this->validator->is_Intitule($intitule, 1, 30))
		{
			$this->erreurs[] = self::INTITULE_INVALIDE;
		}
		
		$this->intitule = $intitule;
	}
	
	public function setDate($date)
	{
		if(!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATE_INVALIDE;
		}
		
		$this->date = $date;
	}
	
	public function setOperation($operation)
	{
		if (!$this->validator->is_Id($operation))
		{
			$this->erreurs[] = self::OPERATION_INVALIDE;
		}
		
		$this->operation = $operation;
	}
	
  
  // GETTERS //
  
	public function date()
	{
		return $this->date;
	}
	
	public function intitule()
	{
		return $this->intitule;
	}
	
	public function operation()
	{
		return $this->operation;
	}
}