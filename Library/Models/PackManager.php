<?php
namespace Library\Models;

use \Library\Entities\Pack;

abstract class PackManager extends \Library\Manager
{
	public function __construct($dao)
	{
		parent::__construct($dao);
		
		$this->entity_database = PREFIX_TABLE.'pack';
	}
	
}