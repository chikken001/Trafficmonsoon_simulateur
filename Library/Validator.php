<?php
namespace Library;

class Validator
{
	public function date_format ($date)
	{
		$date = explode('/', $date) ;
		$jour = $date['0'] ;
		$anne = $date['2'] ;
		$mois = $date['1'] ;
		
		$date = new \DateTime("$anne-$mois-$jour") ;
		$date = $date->format('Y-m-d');
		
		return $date ;
	}
	
	public function datetime_format ($date)
	{
		$date = explode('/', $date) ;
		$jour = $date['0'] ;
		$mois = $date['1'] ;
		$part = $date['2'] ;
		
		$part = explode(' ', $part) ;
		$anne = $part['0'] ;
		$hour = $part['1'] ;
		
		$hour = explode(':', $hour) ;
		$heure = $hour['0'] ;
		$min = $hour['1'] ;
		$sec = '00' ;
		if(isset($hour[2]))
		{
			$sec = $hour['2'] ;
		}
		
		$date = new \DateTime("$anne-$mois-$jour $heure:$min:$sec") ;
		$date = $date->format('Y-m-d H:i:s');
		
		return $date ;
	}
	
	public function is_Siren($siren) 
	{
		$siren = str_replace (' ', '', $siren);
		
		if (strlen ($siren) != 9 || !is_numeric ($siren)) {
			return false;
		}
	   
		$total = 0;
		
		for($i = 0; $i < 9; $i++) {
			
			$temp = substr ( $siren, $i, 1 );
			
			if ($i % 2 == 1) {
				
				$temp *= 2;
				
				if ($temp > 9) {
					
					$temp -= 9;
				}
			}
			$total += $temp;
		}
		return (($total % 10) == 0);
	}

	public function is_Siret($siret) 
	{
		$siret = str_replace ( ' ', '', $siret );
		if (strlen ( $siret ) != 14 || !is_numeric ( $siret )) {
			return false;
		}
	   
		$siren = substr ( $siret, 0, 9 );
		if (! $this->is_Siren ($siren)) {
			return false;
		}
	   
		$total = 0;
		for($i = 0; $i < 14; $i++) {
			
			$temp = substr ( $siret, $i, 1 );
			
			if ($i % 2 == 0) {
				
					$temp *= 2;
					
					if ($temp > 9) {
						
						$temp -= 9;
					}
			}
			$total += $temp;
		}
		return (($total % 10) == 0);
	}
	
	public function is_NumSecu($numero)
	{
		$regexp = '/^                                               # début de chaÃ®ne
		(?<sexe>[1278])                                             # 1 et 7 pour les hommes ou 2 et 8 pour les femmes
		(?<annee>[0-9]{2})                                          # année de naissance
		(?<mois>0[1-9]|1[0-2]|20)                                   # mois de naissance (si >= 20, c\'est qu\'on ne connaissait pas le mois de naissance de la personne
		(?<departement>[02][1-9]|2[AB]|[1345678][0-9]|9[012345789]) # le département : 01 Ã  19, 2A ou 2B, 21 Ã  95, 99 (attention, cas particulier hors métro traité hors expreg)
		(?<numcommune>[0-9]{3})                                     # numéro d\'ordre de la commune (attention car particuler pour hors métro  traité hors expression régulière)
		(?<numacte>00[1-9]|0[1-9][0-9]|[1-9][0-9]{2})               # numéro d\'ordre d\'acte de naissance dans le mois et la commune ou pays
		(?<clef>0[1-9]|[1-8][1-9]|9[1-7])?                          # numéro de contrÃ´le (facultatif)
		$                                                           # fin de chaÃ®ne
		/x';
		
		if(!preg_match($regexp, $numero, $match)){
			return FALSE ;
		}
	 
		$return = array(
			'sexe' => $match['sexe'],//7,8 => homme et femme ayant un num de sécu temporaire
			'annee' =>$match['annee'],//année de naissance + ou - un siècle uhuh
			'mois' =>$match['mois'],//20 = inconnu
			'departement' =>$match['departement'],//99 = étranger
			'numcommune' =>$match['numcommune'],//990 = inconnu
			'numacte' =>$match['numacte'],//001 Ã  999
			'clef' =>isset($match['clef'])?$match['clef']:NULL,//00 Ã  97
			'pays' =>'fra',//par défaut, on change que pour le cas spécifique
		);
	 
		$aChecker = floatval(substr($numero, 0, 13));
	 
		switch(true){
			case $return['departement'] == '2A' :
				$aChecker = floatval(str_replace('A', 0, substr($numero, 0, 13)));
				$aChecker-= 1000000 ;
			break;
			case $return['departement'] == '2B' :
				$aChecker = floatval(str_replace('A', 0, substr($numero, 0, 13)));
				$aChecker-= 2000000 ;
			break;
	 
			case $return['departement'] == 97 || $return['departement'] == 98 :
				$return['departement'].=substr($return['numcommune'], 0, 1);
				$return['numcommune'] = substr($return['numcommune'], 1, 2) ;
				if($return['numcommune'] > 90){//90 = commune inconnue
					return FALSE ;
				}
			break;
	 
			case $return['departement'] == 99 :
				$return['pays'] = $match['numcommune'] ;
				if($return['numcommune'] > 990){//990 = pays inconnu
					return FALSE ;
				}
			break;
	 
			default :
				if($return['numcommune'] > 990){//990 = commune inconnue
					return FALSE ;
				}
			break;
		}
 
		$clef = 97 - fmod($aChecker, 97) ;
	 
		if(empty($return['clef'])){
			$return['clef'] = $clef ; //la clef est optionnelle, si elle n'est pas spécifiée, le numéro est valide, mais on rajoute la clef
		}if($clef != $return['clef']){
			return FALSE ;
		}
		return $return['sexe'].$return['annee'].$return['mois'].$return['departement'].$return['numcommune'].$return['numacte'].$return['clef'] ;
	}
	
	// Valide une chaine de caractère composée seulement de lettre majuscule ou minuscule avec ou sans accents de taille minimum $min et maximum $max
	
	public function is_Nom($nom, $min = 2, $max = 20)
	{
		if(is_int($min) && is_int($max))
		{
			if (!is_string($nom) || empty($nom) || !preg_match('/^[a-zA-ZÃ€Ã�Ã‚ÃƒÃ„Ã…Ã Ã¡Ã¢Ã£Ã¤Ã¥Ã’Ã“Ã”Ã•Ã–Ã˜Ã²Ã³Ã´ÃµÃ¶Ã¸ÃˆÃ‰ÃŠÃ‹èéÃªÃ«Ã‡Ã§ÃŒÃ�ÃŽÃ�Ã¬Ã­Ã®Ã¯Ã™ÃšÃ›ÃœÃ¹ÃºÃ»Ã¼Ã¿Ã‘Ã±Ã¿Å¸]{'.$min.','.$max.'}+$/',$nom))
			{
				return FALSE ;	
			}
			
			return TRUE ;
		}
		else
		{
			throw new \InvalidArgumentException('Les valeurs $min et $max doivent etre des entiers');	
		}
	}
	
	// Valide une chaine de caractère composée de lettre majuscule, minuscule avec ou sans accents et/ou de chiffres ainsi que d'espaces et des caratères suivant : / \ - _ de taille minimum $min et maximum $max
	
	public function is_Intitule($intitule, $min = 1, $max = 30)
	{
		if(is_int($min) && is_int($max))
		{
			if (!is_string($intitule) || empty($intitule) || !preg_match('/^[\sa-zA-Z0-9Ã€Ã�Ã‚ÃƒÃ„Ã…Ã Ã¡Ã¢Ã£Ã¤Ã¥Ã’Ã“Ã”Ã•Ã–Ã˜Ã²Ã³Ã´ÃµÃ¶Ã¸ÃˆÃ‰ÃŠÃ‹èéÃªÃ«Ã‡Ã§ÃŒÃ�ÃŽÃ�Ã¬Ã­Ã®Ã¯Ã™ÃšÃ›ÃœÃ¹ÃºÃ»Ã¼Ã¿Ã‘Ã±Ã¿Å¸\/\\-_]{'.$min.','.$max.'}+$/',$intitule))
			{
				return FALSE ;	
			}
			
			return TRUE ;
		}
		else
		{
			throw new \InvalidArgumentException('Les valeurs $min et $max doivent etre des entiers');	
		}
	}
	
	// Valide une chaine de caractère composée seulement de lettre majuscule ou minuscule avec ou sans accents ou de chiffres de taille minimum $min et maximum $max
	
	public function is_Login($login, $min = 5, $max = 30)
	{
		if(is_int($min) && is_int($max))
		{
			if (!is_string($login) || empty($login) || !preg_match('/^[a-zA-Z0-9Ã€Ã�Ã‚ÃƒÃ„Ã…Ã Ã¡Ã¢Ã£Ã¤Ã¥Ã’Ã“Ã”Ã•Ã–Ã˜Ã²Ã³Ã´ÃµÃ¶Ã¸ÃˆÃ‰ÃŠÃ‹èéÃªÃ«Ã‡Ã§ÃŒÃ�ÃŽÃ�Ã¬Ã­Ã®Ã¯Ã™ÃšÃ›ÃœÃ¹ÃºÃ»Ã¼Ã¿Ã‘Ã±Ã¿Å¸]{'.$min.','.$max.'}+$/',$login))
			{
				return FALSE ;	
			}
			
			return TRUE ;
		}
		else
		{
			throw new \InvalidArgumentException('Les valeurs $min et $max doivent etre des entiers');	
		}
	}
	
	// Valide une chaine de caractère composée au minimum de 2 types de caractère (spéciale, minuscule, majuscule, chiffre) de taille minimum $min et maximum $max
	
	public function is_Password($password, $min = 6, $max = 20)
	{
		if(is_int($min) && is_int($max))
		{
			if (!is_string($password) || empty($password) || !preg_match("((^(?=.*[A-Z])(?=.*[a-z]))|(^(?=.*[a-z])(?=.*[0-9]))|(^(?=.*[a-z])(?=.*[^a-zA-Z0-9]))|(^(?=.*[A-Z])(?=.*[0-9]))|(^(?=.*[A-Z])(?=.*[^a-zA-Z0-9]))|(^(?=.*[0-9])(?=.*[^a-zA-Z0-9])))",$password) || strlen($password) < $min || strlen($password) > $max)
			{
				return FALSE ;	
			}
			
			return TRUE ;
		}
		else
		{
			throw new \InvalidArgumentException('Les valeurs $min et $max doivent etre des entiers');	
		}
	}
	
	public function is_Spectacle($spectacle)
	{
		if (!is_string($spectacle) || empty($spectacle) || !preg_match("/[a-zA-Z-0-9]{6}$/",$spectacle))
		{
			return FALSE ;	
		}
		
		return TRUE ;
	}
	
	public function is_Tel($tel)
	{
		$esp = array("-" => "", "_" => "", " " => "" , "," => "", "." => "", "+33" => "0");
		$tel = strtr($tel, $esp);
	
		if (!is_string($tel) || !preg_match("/(0)[0-9]{9}$/",$tel) || empty($tel))
		{
			return FALSE ;
		}

		return TRUE ;
	}
	
	public function is_Id($id)
	{
		if (!is_int($id) && !ctype_digit($id))
		{
			if(is_string($id))
			{
				if(preg_match("[new_]",$id))
				{
					$id = substr($id, 4) ;
					
					if (!ctype_digit($id))
					{
						return FALSE ;
					}
				}
				else
				{
					return FALSE ;
				}
			}
			else
			{
				return FALSE ;
			}
		}
		
		if(intval($id) > 2147483647)
		{
			return FALSE ;
		}

		return TRUE ;
	}
	
	public function is_Portable($portable)
	{
		$esp = array("-" => "", "_" => "", " " => "" , "," => "", "." => "", "+33" => "0");
		$portable = strtr($portable, $esp);
	
		if (!is_string($portable) || empty($portable) || !preg_match("/(06|07)[0-9]{8}$/",$portable))
		{
			return FALSE ;
		}
		
		return TRUE ;
	}
	
	public function is_Email($email)
	{
		if (!is_string($email) || empty($email) || !preg_match("/([\w\d\-\.]+)@{1}(([\w\d\-]{1,67})|([\w\d\-]+\.[\w\d\-]{1,67}))\.(([a-zA-Z\d]{2,4})(\.[a-zA-Z\d]{2})?)$/", $email))
		{
			return FALSE ;	
		}
		
		return TRUE ;
	}
	
	public function is_Date($date, $type = 'date')
	{
		if(!$date instanceof \DateTime && date_parse_from_format("Y-m-d", $date)['error_count'] != 0 && date_parse_from_format("Y-m-d H:i:s", $date)['error_count'] != 0)
		{
			if(is_string($date) && !empty($date))
			{
				if(preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$)/',$date) && $type === 'datetime')
				{
					$Date = $this->datetime_format($date) ;
					$format = "Y-m-d H:i:s" ;
				}
				elseif(preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4}$)/',$date) && $type === 'date')
				{
					$Date = $this->date_format($date) ;
					$format = "Y-m-d" ;
				}
				else
				{
					return FALSE ;
				}
				
				if(date_parse_from_format($format, $Date)['error_count'] != 0)
				{
					return FALSE ;
				}
			}
			else
			{
				return FALSE ;
			}
		}
		
		return TRUE ;
	}
}