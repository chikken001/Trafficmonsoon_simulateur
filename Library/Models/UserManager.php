<?php
namespace Library\Models;

use \Library\Entities\User;

abstract class UserManager extends \Library\Manager
{
	public function __construct($dao)
	{
		parent::__construct($dao);
		
		$this->entity_database = PREFIX_TABLE.'user';
	}
	
}