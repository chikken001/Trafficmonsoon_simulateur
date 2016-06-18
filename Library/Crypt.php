<?php
namespace Library;

class Crypt
{
	protected $specialChar1 = array ('@','#','à','é','è','%','/','=','+','-','æ','ã','ç','*','(',')','-','_','$','ù','£','&','ë','ï','ÿ','¥','.',',',';',':') ; 
	protected $specialChar2 = array ('â','Î','¿','À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ð','Ñ','Ò','Ó','Ô','§','¢','ñ','ø','Ø','÷','Õ','!','?','ß') ; 
	protected $lettre = array ('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	
	function pass($password, $salt, $key)
	{
		$part1 = substr($salt, 0, 5) ;
		$part2 = substr($salt, 6, 10) ;
		$part3 = substr($key, 0, 5) ;
		$part4 = substr($key, 6, 10) ;
		
		$password = hash('sha512',$part1.$part3.$password.$part4.$part2) ;
		
		return $password ;
	}
	
	function salt()
	{
		$tab = array() ;
		$temp = array() ;
		$final = array() ;
		$salt ='';
		
		$tab[] = rand(0,9);
		$tab[] = rand(0,9);
		$tab[] = rand(0,9);
		$tab[] = $this->lettre[rand(0,25)];
		$tab[] = $this->lettre[rand(0,25)];
		$tab[] = strtoupper($this->lettre[rand(0,25)]);
		$tab[] = strtoupper($this->lettre[rand(0,25)]);
		$tab[] = $this->specialChar1[rand(0,29)];
		$tab[] = $this->specialChar1[rand(0,29)];
		$tab[] = $this->specialChar2[rand(0,29)];
		
		while (count($temp) < 10)
		{
			$nb = rand(0,9);
			
			if (!in_array($nb, $temp)) {
				
				$temp[] = $nb ;
				$final[] = $tab[$nb];
			}
		}
		
		$count = 0 ;
		
		foreach ($final as $value) {
			$salt .= $value ;
		}
		
		return $salt ;
	}
	
	function encrypt($data, $cle = "M:5s@p7e") { // Clé de 8 caractères max
	
		if(!empty($data) && (is_string($data) || is_int($data)))
		{
			$data = serialize($data);
			$td = mcrypt_module_open(MCRYPT_DES,"",MCRYPT_MODE_ECB,"");
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
			mcrypt_generic_init($td,$cle,$iv);
			$data = mcrypt_generic($td, '!'.$data) ;
			$data = rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
			mcrypt_generic_deinit($td);
			if($data == "C4VJL9uDRbo=")
				$data ="";
				
			return $data;
		}
		
		return false ;
	}
	 
	function decrypt($data, $cle = "M:5s@p7e") { // Clé de 8 caractères max
		
		$def = $data ;
		
		if(!empty($data) && (is_string($data) || is_int($data)))
		{
			$td = mcrypt_module_open(MCRYPT_DES,"",MCRYPT_MODE_ECB,"");
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
			mcrypt_generic_init($td,$cle,$iv);
			$data = base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
			if(empty($data))
				return $def ;
				
			$data = mdecrypt_generic($td, $data);
			mcrypt_generic_deinit($td);
		 
			if (substr($data,0,1) != '!')
				return $def;
		 
			$data = substr($data,1,strlen($data)-1);
			
			return unserialize($data);
		}
		
		return $data ;
	}
    
    function mdp()
	{
        $tab = array() ;
		$temp = array() ;
		$final = array() ;
		$mdp ='';
		
		$tab[] = rand(0,9);
		$tab[] = rand(0,9);
		$tab[] = rand(0,9);
		$tab[] = $this->lettre[rand(0,25)];
		$tab[] = $this->lettre[rand(0,25)];
		$tab[] = strtoupper($this->lettre[rand(0,25)]);
		$tab[] = strtoupper($this->lettre[rand(0,25)]);
		$tab[] = $this->lettre[rand(0,25)];
		$tab[] = $this->lettre[rand(0,25)];
		$tab[] = $this->lettre[rand(0,25)];
		
		while (count($temp) < 10)
		{
			$nb = rand(0,9);
			
			if (!in_array($nb, $temp)) {
				
				$temp[] = $nb ;
				$final[] = $tab[$nb];
			}
		}
		
		$count = 0 ;
		
		foreach ($final as $value) {
			$mdp .= $value ;
		}
		
		return $mdp ;
    }
}