<?php
namespace Library\Entities;

class Logistique extends \Library\Entity
{
	protected $ressource,
			  $debut,
			  $fin;
	
	const RESSOURCE_INVALIDE = 2;
	const DEBUT_INVALIDE = 3;
	const FIN_INVALIDE = 4;
	
	const DEBUT_IDENTIQUE = 5;
	const FIN_IDENTIQUE = 6;
  
  // SETTERS //
	
	public function setRessource($ressource)
	{
		if (!$this->validator->is_Id($ressource))
		{
			$this->erreurs[] = self::COMPETENCE_INVALIDE;
		}
		
		$this->ressource = $ressource;
	}
	
	public function setDebut($debut)
	{
		if (!$this->validator->is_Id($debut))
		{
			$this->erreurs[] = self::DEBUT_INVALIDE;
		}
		
		$this->debut = $debut;
	}
	
	public function setFin($fin)
	{
		if (!$this->validator->is_Id($fin))
		{
			$this->erreurs[] = self::FIN_INVALIDE;
		}
		
		$this->fin = $fin;
	}
	
  
  // GETTERS //
  
	public function debut()
	{
		return $this->debut;
	}
	
	public function fin()
	{
		return $this->fin;
	}
	
	public function ressource()
	{
		return $this->ressource;
	}	
}