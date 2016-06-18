<?php
namespace Library;

use \Library\Crypt ;

class User extends ApplicationComponent
{
	public function getAttribute($attr)
	{
		return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
	}
	
	public function setAttribute($attr, $value)
	{
		$_SESSION[$attr] = $value;
	}
	
	public function deleteAttribute($attr)
	{
		if(isset($_SESSION[$attr]))
		{
			unset($_SESSION[$attr]) ;
		}
	}
	
	public function getFlash()
	{
		$flash = $_SESSION['flash'];
		unset($_SESSION['flash']);
		
		return $flash;
	}
	
	public function key()
	{
		if(isset($_SESSION['key']))
		{ 
			return $_SESSION['key'] ;
		}
		
		return false ;
	}
	
	public function setKey()
	{
		if(!isset($_SESSION['key']))
		{
			$crypt = new Crypt ;
			$key = substr($crypt->salt(), 0 , 8) ;
			$_SESSION['key'] = $key ; 
		}
	}
	
	public function hasFlash()
	{
		return isset($_SESSION['flash']);
	}
	
	public function isAuthenticated()
	{
		return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
	}
	
	public function setAuthenticated($authenticated = true)
	{
		if (!is_bool($authenticated))
		{
			throw new \InvalidArgumentException('Les valeurs specifiees a la methode User::setAuthenticated() doivent etre valide');
		}
		
		$_SESSION['auth'] = $authenticated;
	}
	
	public function setFlash($value)
	{
		$_SESSION['flash'] = $value;
	}
	
	public function generateToken($nom)
	{
		$token = uniqid(rand(), true);
		$_SESSION[$nom.'_token'] = $token;
		$_SESSION[$nom.'_token_time'] = time();
		return $token;
	}
	
	public function isValidToken($temps, $referer, $nom)
	{
		if(isset($_SESSION[$nom.'_token']) && isset($_SESSION[$nom.'_token_time']) && isset($_POST['token']))
		{
			if($_SESSION[$nom.'_token'] == $_POST['token'])
			{
				if($_SESSION[$nom.'_token_time'] >= (time() - $temps))
				{
					if($_SERVER['HTTP_REFERER'] == $referer)
					{
						return true;
					}
				}
			}
		}
		return false;
	}
}