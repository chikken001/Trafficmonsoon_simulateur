<?php
namespace Applications\App;
class AppApplication extends \Library\Application
{
	public function __construct()
	{
		parent::__construct();
		
		$this->name = 'App';
	}
	
	public function run()
	{
		parent::run();
	}
}