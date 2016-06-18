<?php
namespace Library\Entities;

class Ressource extends \Library\Entity
{
	protected $nom,
			  $prestataire;

	const NOM_INVALIDE = 1;
	const PRESTATAIRE_INVALIDE = 2;

  
  // SETTERS //
  
	public function setNom($nom)
	{
		if (!$this->validator->is_Intitule($nom))
		{
			$this->erreurs[] = self::NOM_INVALIDE;
		}
		
		$this->nom = $nom;
	}
	
	public function setPrestataire($prestataire)
	{
		if (!$this->validator->is_Id($prestataire))
		{
			$this->erreurs[] = self::PRESTATAIRE_INVALIDE;
		}
		
		$this->prestataire = $prestataire;
	}
  
  // GETTERS //
  
	public function nom()
	{
		return $this->nom;
	}
	
	public function prestataire()
	{
		return $this->prestataire;
	}
}