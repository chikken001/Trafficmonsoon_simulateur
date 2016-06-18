<?php
namespace Library;

use \Library\Validator ;

abstract class Entity implements \ArrayAccess
{
	protected $erreurs = array(),
		  	$id,
		  	$validator ;
	
	public function __construct(array $donnees = array())
	{
		$this->validator = new Validator ;
		
		if (!empty($donnees))
		{
			$this->hydrate($donnees);
		}
	}
	
	public function isValid()
	{
		return (count($this->erreurs) === 0);
	}
	
	public function isNew()
	{
		return empty($this->id) || preg_match('/^new_/', $this->id);
	}
	
	public function erreurs()
	{
		return $this->erreurs;
	}
	
	public function id()
	{
		return $this->id;
	}
	
	public function setId($id = false)
	{
		if(preg_match('/^new_/',$id))
		{
			$this->id = $id;
		}
		elseif($id === false)
		{
			$this->id = '' ;
		}
		else
		{
			$this->id = (int) $id;
		}
	}
	
	public function remove_erreurs()
	{
		$this->erreurs = array() ;
	}
	
	public function hydrate(array $donnees)
	{
		foreach ($donnees as $attribut => $valeur)
		{
			$methode = 'set'.ucfirst($attribut);
			
			if (is_callable(array($this, $methode)))
			{
				$this->$methode($valeur);
			}
		}
	}
	
	public function getVars() {
    	return get_object_vars($this) ;
    }
	
	public function offsetGet($var)
	{
		if (isset($this->$var) && is_callable(array($this, $var)))
		{
			return $this->$var();
		}
	}
	
	public function offsetSet($var, $value)
	{
		$method = 'set'.ucfirst($var);
		
		if (isset($this->$var) && is_callable(array($this, $method)))
		{
			$this->$method($value);
		}
	}
	
	public function offsetExists($var)
	{
		return isset($this->$var) && is_callable(array($this, $var));
	}
	
	public function offsetUnset($var)
	{
		throw new \Exception('Impossible de supprimer une quelconque valeur');
	}
}