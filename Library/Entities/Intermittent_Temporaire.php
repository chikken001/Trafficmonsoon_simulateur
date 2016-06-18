<?php
namespace Library\Entities;

use \Library\Crypt ;

class Intermittent_Temporaire extends \Library\Entity
{
	protected $nom,
			  $prenom,
			  $spectacle,
			  $social,
			  $portable,
			  $tel,
			  $email,
			  $prestataire,
			  $note,
			  $statut,
			  $adresse,
			  $naissance,
			  $code_postal,
			  $ville,
			  $pays;
	
	const NOM_INVALIDE = 1;
	const SOCIAL_INVALIDE = 2;
	const TEL_INVALIDE = 3;
	const EMAIL_INVALIDE = 4;
	const PRENOM_INVALIDE = 5;
	const NAISSANCE_INVALIDE = 6;
	const SPECTACLE_INVALIDE = 7;
	const PORTABLE_INVALIDE = 8;
	const PRESTATAIRE_INVALIDE = 9;
	const NOTE_INVALIDE = 10;
	const STATUT_INVALIDE = 11;
	const ADRESSE_INVALIDE = 12;
	const CODE_POSTAL_INVALIDE = 13;
	const VILLE_INVALIDE = 14;
	const PAYS_INVALIDE = 15;
	
	public function isValid()
	{
		if($this->statut == null) $this->statut = 2 ;
		
		return (count($this->erreurs) === 0);
	}
  
  
  // SETTERS //
  
	public function setNom($nom)
	{
		if (!$this->validator->is_Nom($nom))
		{
			$this->erreurs[] = self::NOM_INVALIDE;
		}
		
		$this->nom = $nom;
	}
	
	public function setNote($note)
	{
		if (!is_string($note))
		{
			$this->erreurs[] = self::NOTE_INVALIDE;
		}
		
		$this->note = $note;
	}
	
	public function setStatut($statut)
	{
		$statuts = array('0','1','2') ;
		
		if (!in_array($statut,$statuts))
		{
			$this->erreurs[] = self::STATUT_INVALIDE;
		}
		
		$this->statut = $statut;
	}
	
	public function setPrenom($prenom)
	{
		if (!$this->validator->is_Nom($prenom))
		{
			$this->erreurs[] = self::PRENOM_INVALIDE;
		}
		
		$this->prenom = $prenom;
	}
	
	public function setSocial($social)
	{
		if(!empty($social))
		{
			$verif = $this->validator->is_NumSecu($social) ;
			
			if(!$verif)
			{
				//$this->erreurs[] = self::SOCIAL_INVALIDE;
				$this->social = $social;
			}
			else
			{
				$this->social = $verif;
			}
		}
	}
	
	public function setAdresse($adresse)
	{
		if (!is_string($adresse) || empty($adresse) || strlen($adresse) < 10)
		{
			$this->erreurs[] = self::ADRESSE_INVALIDE;
		}
		
		$this->adresse = $adresse;
	}
	
	public function setPays($pays)
	{
		if (!$this->validator->is_Nom($pays))
		{
			$this->erreurs[] = self::PAYS_INVALIDE;
		}
		
		$this->pays = $pays;
	}
	
	public function setVille($ville)
	{
		if (!$this->validator->is_Nom($ville))
		{
			$this->erreurs[] = self::VILLE_INVALIDE;
		}
		
		$this->ville = $ville;
	}
	
	public function setCode_postal($code_postal)
	{
		if (!preg_match("^([0-9]{5})$", $code_postal))
		{
			$this->erreurs[] = self::CODE_POSTAL_INVALIDE;
		}
		
		$this->code_postal = $code_postal;
	}
	
	public function setSpectacle($spectacle)
	{
		if(!$this->validator->is_Spectacle($spectacle) && !empty($spectacle))
		{
			$this->erreurs[] = self::SPECTACLE_INVALIDE;
		}
		
		$this->spectacle = $spectacle;
	}
	
	public function setTel($tel)
	{
		if(!$this->validator->is_Tel($tel) && !empty($tel))
		{
			$this->erreurs[] = self::TEL_INVALIDE;
		}

		$this->tel = $tel;
	}
	
	public function setPortable($portable)
	{
		if(!$this->validator->is_Portable($portable) && !empty($portable))
		{
			$this->erreurs[] = self::PORTABLE_INVALIDE;
		}

		$this->portable = $portable;
	}
	
	public function setEmail($email)
	{
		if(!$this->validator->is_Email($email))
		{
			$this->erreurs[] = self::EMAIL_INVALIDE;
		}
		
		$this->email = $email;
		
	}
	
	public function setNaissance($naissance)
	{
		if(!$this->validator->is_Date($naissance, 'date') && !empty($naissance))
		{
			$this->erreurs[] = self::NAISSANCE_INVALIDE;
		}
		elseif(empty($naissance))
		{
			$naissance = false ;	
		}
		
		$this->naissance = $naissance ;
	}
	
	public function setPrestataire($prestataire)
	{
		if(!$this->validator->is_Id($prestataire))
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
	
	public function prenom()
	{
		return $this->prenom;
	}
	
	public function note()
	{
		return $this->note;
	}
	
	public function statut()
	{
		return $this->statut;
	}
	
	public function portable()
	{
		return $this->portable;
	}
	
	public function social()
	{
		return $this->social;
	}
	
	public function spectacle()
	{
		return $this->spectacle;
	}
	
	public function tel()
	{
		return $this->tel;
	}
	
	public function email()
	{
		return $this->email;
	}
	
	public function naissance()
	{
		return $this->naissance;
	}
	
	public function adresse()
	{
		return $this->adresse;
	}
	
	public function ville()
	{
		return $this->ville;
	}
	
	public function pays()
	{
		return $this->pays;
	}
	
	public function code_postal()
	{
		return $this->code_postal;
	}
	
	public function prestataire()
	{
		return $this->prestataire;
	}
}