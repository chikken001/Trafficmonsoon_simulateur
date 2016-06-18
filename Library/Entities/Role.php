<?php
namespace Library\Entities;

class Role extends \Library\Entity
{
	protected $debut,
			  $fin,
			  $technicien,
			  $technicien_temporaire;
	
	const COMPETENCE_INVALIDE = 2;
	const DEBUT_INVALIDE = 3;
	const FIN_INVALIDE = 4;
	const TECHNICIEN_INVALIDE = 5;
	const LOGISTIQUE_INVALIDE = 6;
	const TECHNICIEN_TEMPORAIRE_INVALIDE = 9;
	
	const DEBUT_IDENTIQUE = 7;
	const FIN_IDENTIQUE = 8;
	
	const TECHNICIEN_TEMPORAIRE_INDISPO = 10;
	const TECHNICIEN_INDISPO = 11;
	
	public function isValid()
	{
		if($this->debut == $this->fin)
		{
			$this->erreurs[] = self::DEBUT_IDENTIQUE;
			$this->erreurs[] = self::FIN_IDENTIQUE;
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
		
		if(!empty($this->technicien_temporaire))
		{
			$this->erreurs[] = self::TECHNICIEN_INDISPO;
		}
		
		$this->technicien = $technicien;
	}
	
	public function setTechnicien_temporaire($technicien = false)
	{
		if (!$this->validator->is_Id($technicien) && $technicien != false)
		{
			$this->erreurs[] = self::TECHNICIEN_TEMPORAIRE_INVALIDE;
		}
		
		if(!empty($this->technicien))
		{
			$this->erreurs[] = self::TECHNICIEN_TEMPORAIRE_INDISPO;
		}
		
		$this->technicien_temporaire = $technicien;
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
	
	public function technicien()
	{
		return $this->technicien;
	}
	
	public function technicien_temporaire()
	{
		return $this->technicien_temporaire;
	}
}