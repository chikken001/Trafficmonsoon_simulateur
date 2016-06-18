<?php
namespace Library\Entities;

class Prestataire extends \Library\Entity
{
	protected $nom,
			  $password,
			  $siret,
			  $tel,
			  $email,
			  $adresse,
			  $presentation,
			  $dateinscription,
			  $salt,
			  $login;
	
	const NOM_INVALIDE = 1;
	const PASSWORD_INVALIDE = 2;
	const SIRET_INVALIDE = 3;
	const TEL_INVALIDE = 4;
	const EMAIL_INVALIDE = 5;
	const ADRESSE_INVALIDE = 6;
	const PRESENTATION_INVALIDE = 7;
	const LOGIN_INVALIDE = 8;
	const DATEINSCRIPTION_INVALIDE = 13 ;
	
	const PASSWORD_VERIFICATION = 9;
	
	const SIRET_INDISPONIBLE = 10;
	const EMAIL_INDISPONIBLE = 11;
	const LOGIN_INDISPONIBLE = 12;
  
  // SETTERS //
  
	public function setNom($nom)
	{
		if (!$this->validator->is_Intitule($nom))
		{
			$this->erreurs[] = self::NOM_INVALIDE;
		}
		
		$this->nom = $nom;
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
	
	public function setSiret($siret)
	{
		if (!$this->validator->is_Siret($siret))
		{
			$this->erreurs[] = self::SIRET_INVALIDE;
		}
		
		$this->siret = $siret;
	}
	
	public function setTel($tel)
	{
		if(!$this->validator->is_Tel($tel))
		{
			$this->erreurs[] = self::TEL_INVALIDE;
		}

		$this->tel = $tel;
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
	
	public function setDateinscription($dateinscription)
	{
		if(!$this->validator->is_Date($dateinscription, 'date'))
		{
			$this->erreurs[] = self::DATEINSCRIPTION_INVALIDE;
		}
		
		$this->dateinscription = $dateinscription ;
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
	
	public function siret()
	{
		return $this->siret;
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
	
	public function presentation()
	{
		return $this->presentation;
	}
	
	public function dateinscription()
	{
		return $this->dateinscription;
	}
}