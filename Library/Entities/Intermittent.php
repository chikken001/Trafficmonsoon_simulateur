<?php
namespace Library\Entities;

use \Library\Crypt ;

class Intermittent extends \Library\Entity
{
	protected $nom,
			  $prenom,
			  $nationalite,
			  $spectacle,
			  $portable,
			  $password,
			  $social,
			  $tel,
			  $email,
			  $adresse,
			  $presentation,
			  $naissance,
			  $salt,
			  $login,
			  $dateinscription,
			  $code_postal,
			  $ville,
			  $pays;
	
	const NOM_INVALIDE = 1;
	const PASSWORD_INVALIDE = 2;
	const SOCIAL_INVALIDE = 3;
	const TEL_INVALIDE = 4;
	const EMAIL_INVALIDE = 5;
	const ADRESSE_INVALIDE = 6;
	const PRESENTATION_INVALIDE = 7;
	const LOGIN_INVALIDE = 8;
	const PRENOM_INVALIDE = 9;
	const NAISSANCE_INVALIDE = 10;
	const NATIONALITE_INVALIDE = 11;
	const SPECTACLE_INVALIDE = 12;
	const PORTABLE_INVALIDE = 13;
	const DATEINSCRIPTION_INVALIDE = 14 ;
	const CODE_POSTAL_INVALIDE = 20;
	const VILLE_INVALIDE = 21;
	const PAYS_INVALIDE = 22;
	
	const SPECTACLE_INDISPONIBLE = 15;
	const LOGIN_INDISPONIBLE = 16;
	const EMAIL_INDISPONIBLE = 17;
	const SOCIAL_INDISPONIBLE = 18;
	
	const PASSWORD_VERIFICATION = 19;
  
  
  // SETTERS //
  
	public function setNom($nom)
	{
		if (!$this->validator->is_Nom($nom))
		{
			$this->erreurs[] = self::NOM_INVALIDE;
		}
		
		$this->nom = $nom;
	}
	
	public function setPrenom($prenom)
	{
		if (!$this->validator->is_Nom($prenom))
		{
			$this->erreurs[] = self::PRENOM_INVALIDE;
		}
		
		$this->prenom = $prenom;
	}
	
	public function setNationalite($nationalite)
	{
		if (!$this->validator->is_Nom($nationalite))
		{
			$this->erreurs[] = self::NATIONALITE_INVALIDE;
		}
		
		$this->nationalite = $nationalite;
	}
	
	public function setLogin($login)
	{
		if (!$this->validator->is_Login($login))
		{
			$this->erreurs[] = self::LOGIN_INVALIDE;
		}
		
		$this->login = $login;
	}
	
	public function setPassword($password)
	{
		if (!$this->validator->is_Password($password))
		{
			$this->erreurs[] = self::PASSWORD_INVALIDE;
		}
		else
		{
			$this->password = $password;
		}
	}
	
	public function setSocial($social)
	{
		$verif = $this->validator->is_NumSecu($social) ;
		
		/*if(!$verif)
		{
			$this->erreurs[] = self::SOCIAL_INVALIDE;
			$this->social = $social;
		}
		else
		{
			$this->social = $verif;
		}*/
		
		$this->social = $social;
	}
	
	public function setSpectacle($spectacle)
	{
		if(!$this->validator->is_Spectacle($spectacle))
		{
			$this->erreurs[] = self::SPECTACLE_INVALIDE;
		}
		
		$this->spectacle = $spectacle;
	}
	
	public function setTel($tel)
	{
		if(!$this->validator->is_Tel($tel))
		{
			$this->erreurs[] = self::TEL_INVALIDE;
		}

		$this->tel = $tel;
	}
	
	public function setPortable($portable)
	{
		if(!$this->validator->is_Portable($portable))
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
	
	public function setPresentation($presentation)
	{
		if(strlen($presentation) > 0)
		{
			if(!is_string($presentation))
			{
				$this->erreurs[] = self::PRESENTATION_INVALIDE;
			}
		}
		
		$this->presentation = $presentation ;
	}
	
	public function setNaissance($naissance)
	{
		if(!$this->validator->is_Date($naissance, 'date'))
		{
			$this->erreurs[] = self::NAISSANCE_INVALIDE;
		}
		
		$this->naissance = $naissance ;
	}
	
	public function setDateinscription($date)
	{
		if(!$this->validator->is_Date($date, 'date'))
		{
			$this->erreurs[] = self::DATE_INSCRIPTION_INVALIDE;
		}
		
		$this->dateinscription = $date ;
	}
	
	public function setSalt($salt)
	{
		if(strlen($salt) == 10)
		{
			$this->salt = (string)$salt ;
		}
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
	
	public function nationalite()
	{
		return $this->nationalite;
	}
	
	public function portable()
	{
		return $this->portable;
	}
	
	public function login()
	{
		return $this->login;
	}
	
	public function salt()
	{
		return $this->salt;
	}
	
	public function password()
	{
		return $this->password;
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
	
	public function presentation()
	{
		return $this->presentation;
	}
	
	public function naissance()
	{
		return $this->naissance;
	}
	
	public function dateinscription()
	{
		return $this->dateinscription;
	}
}