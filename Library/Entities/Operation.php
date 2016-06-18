<?php
namespace Library\Entities;

class Operation extends \Library\Entity
{
	protected $prestataire,
			  $nom,
			  $pays,
			  $code_postal,
			  $adresse,
			  $client,
			  $agence,
			  $note,
			  $ville,
			  $ref_externe,
			  $ref_interne;
	
	const PRESTATAIRE_INVALIDE = 1;
	const NOM_INVALIDE = 2;
	const PAYS_INVALIDE = 3;
	const CODE_POSTAL_INVALIDE = 4;
	const ADRESSE_INVALIDE = 5;
	const CLIENT_INVALIDE = 6;
	const NOTE_INVALIDE = 7;
	const VILLE_INVALIDE = 8;
	const REF_EXTERNE_INVALIDE = 9;
	const REF_INTERNE_INVALIDE = 10;
	const AGENCE_INVALIDE = 11;
  
  // SETTERS //
  
	public function setNom($nom)
	{
		if (!$this->validator->is_Intitule($nom, 2, 50))
		{
			$this->erreurs[] = self::NOM_INVALIDE;
		}
		
		$this->nom = $nom;
	}
	
	public function setClient($client)
	{
		if (!$this->validator->is_Intitule($client, 2, 50) && !empty($client))
		{
			$this->erreurs[] = self::CLIENT_INVALIDE;
		}
		
		$this->client = $client;
	}
	
	public function setAgence($agence)
	{
		if (!$this->validator->is_Intitule($agence, 2, 50) && !empty($agence))
		{
			$this->erreurs[] = self::AGENCE_INVALIDE;
		}
		
		$this->agence = $agence;
	}
	
	public function setPrestataire($prestataire)
	{
		if(!$this->validator->is_Id($prestataire))
		{
			$this->erreurs[] = self::PRESTATAIRE_INVALIDE;
		}
		
		$this->prestataire = $prestataire;
	}
	
	public function setPays($pays)
	{
		if (!$this->validator->is_Nom($pays, 2 , 50) && !empty($pays))
		{
			$this->erreurs[] = self::PAYS_INVALIDE;
		}
		
		$this->pays = $pays;
	}
	
	public function setCode_postal($code_postal)
	{
		if (!preg_match("/^([0-9]{5})$/", $code_postal) && !empty($code_postal))
		{
			$this->erreurs[] = self::CODE_POSTAL_INVALIDE;
		}
		
		$this->code_postal = $code_postal;
	}
	
	public function setVille($ville)
	{
		if (!$this->validator->is_Intitule($ville, 2 , 50) && !empty($ville))
		{
			$this->erreurs[] = self::VILLE_INVALIDE;
		}
		
		$this->ville = $ville;
	}
	
	public function setAdresse($adresse)
	{
		if ((!is_string($adresse) || strlen($adresse) < 10) && !empty($adresse))
		{
			$this->erreurs[] = self::ADRESSE_INVALIDE;
		}
		
		$this->adresse = $adresse;
	}
	
	public function setNote($note)
	{
		if(strlen($note) > 0)
		{
			if(!is_string($note))
			{
				$this->erreurs[] = self::NOTE_INVALIDE;
			}
		}
		
		$this->note = $note ;
	}
	
	public function setRef_interne($ref_interne)
	{
		if (!is_string($ref_interne))
		{
			$this->erreurs[] = self::REF_INTERNE_INVALIDE;
		}
		
		$this->ref_interne = $ref_interne;
	}
	
	public function setRef_externe($ref_externe)
	{
		if (!is_string($ref_externe))
		{
			$this->erreurs[] = self::REF_EXTERNE_INVALIDE;
		}
		
		$this->ref_externe = $ref_externe;
	}
  
  // GETTERS //
  
	public function prestataire()
	{
		return $this->prestataire;
	}
	
	public function nom()
	{
		return $this->nom;
	}
	
	public function pays()
	{
		return $this->pays;
	}
	
	public function code_postal()
	{
		return $this->code_postal;
	}
	
	public function ville()
	{
		return $this->ville;
	}
	
	public function adresse()
	{
		return $this->adresse;
	}
	
	public function client()
	{
		return $this->client;
	}
	
	public function agence()
	{
		return $this->agence;
	}
	
	public function note()
	{
		return $this->note;
	}
	
	public function ref_interne()
	{
		return $this->ref_interne;
	}
	
	public function ref_externe()
	{
		return $this->ref_externe;
	}
}