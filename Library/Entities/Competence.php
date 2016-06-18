<?php
namespace Library\Entities;

class Competence extends \Library\Entity
{
	protected $type ;

	const TYPE_INVALIDE = 1;

  
  // SETTERS //
  
	public function setType($type)
	{
		$types = array('assistant son', 'assistant lumière', 'assistant structure', 'assistant vidéo', 'technicien son', 'technicien lumière', 'technicien structure', 'technicien vidéo', 'road') ;
		
		if (!in_array($type, $types))
		{
			$this->erreurs[] = self::TYPE_INVALIDE;
		}
		
		$this->type = $type;
	}
  
  // GETTERS //
  
	public function type()
	{
		return $this->type;
	}
}