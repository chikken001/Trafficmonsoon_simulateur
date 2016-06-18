<?php
namespace Library\Models;

use \Library\Entities\Pack;
use \Library\Models\Defaut\Pdo;
use \Library\Crypt ;

class PackManager_PDO extends PackManager
{
	public function __construct($dao)
	{
		parent::__construct($dao);
		$this->DEF = new Pdo($this) ;
	}
}