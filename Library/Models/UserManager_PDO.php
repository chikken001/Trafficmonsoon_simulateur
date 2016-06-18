<?php
namespace Library\Models;

use \Library\Entities\User;
use \Library\Models\Defaut\Pdo;
use \Library\Crypt ;

class UserManager_PDO extends UserManager
{
	public function __construct($dao)
	{
		parent::__construct($dao);
		$this->DEF = new Pdo($this) ;
	}
}