<?php
namespace Library\Entities;

class Maitrise extends \Library\Entity
{
	protected $competence,
			  $nom;
	
	const NOM_INVALIDE = 1;
	const COMPETENCE_INVALIDE = 2;
  
  // SETTERS //
  
	public function setNom($nom)
	{
		if (ctype_digit($nom) || !is_string($nom) || strlen($nom) < 4)
		{
			$this->erreurs[] = self::NOM_INVALIDE;
		}
		
		$this->nom = $nom;
	}
	
	public function setCompetence($competence)
	{
		if (!$this->validator->is_Id($competence))
		{
			$this->erreurs[] = self::COMPETENCE_INVALIDE;
		}
		
		$this->competence = $competence;
	}
	
  
  // GETTERS //
  
	public function competence()
	{
		return $this->competence;
	}
	
	public function nom()
	{
		return $this->nom;
	}
}