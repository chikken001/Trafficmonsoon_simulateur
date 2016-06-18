<?php
namespace Library;

use \Library\Crypt ;

abstract class BackController extends ApplicationComponent
{
	protected $action = '';
	protected $module = '';
	protected $page = null;
	protected $view = '';
	protected $managers = null;
	protected $mode ;
	protected $encrypt_key;
	protected $user;
	protected $layout = 'layout' ; 
	
	public function __construct(Application $app, $module, $action, $layout = 'layout')
	{
		parent::__construct($app);
		
		$this->user = $this->app->user() ;
		
		$dbname = $this->app->config()->getGlobal('dbname') ;
		$dblogin = $this->app->config()->getGlobal('dblogin') ;
		$dbpassword = $this->app->config()->getGlobal('dbpassword') ;
		$host = $this->app->config()->getGlobal('host') ;
		$this->mode = $this->app->config()->getGlobal('mode') ;
		$this->encrypt_key = $this->app->config()->getGlobal('encrypt_key') ;
		$this->layout = $layout ;
		
		$API = $this->app->config()->getGlobal('API') ;
		$API =='PDO' ? $method = PDOFactory::getMysqlConnexion($dbname, $dblogin, $dbpassword, $host) : $method = MySQLiFactory::getMysqlConnexion($dbname, $dblogin, $dbpassword, $host) ;
				
		$this->managers = new Managers($API, $method);
		
		$this->page = new Page($app, $this->layout);
		
		$this->setModule($module);
		$this->setAction($action);
		$this->setView($action);
	}
	
	public function em($manager)
	{
		if(empty($manager) || !is_string($manager) )
		{
			throw new \InvalidArgumentException('Le manager doit etre une chaine de caracteres valide');
		}
		
		return $this->managers->getManagerOf($manager) ;
	}
	
	public function execute()
	{
		$method = 'execute'.ucfirst($this->action);
		
		if (!is_callable(array($this, $method)))
		{
			throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas definie sur le module '.$this->module);
		}
		
		$this->$method($this->app->httpRequest());
	}
	
	public function page()
	{
		return $this->page;
	}
	
	public function setModule($module)
	{
		if (!is_string($module) || empty($module))
		{
			throw new \InvalidArgumentException('Le module doit être une chaine de caractères valide');
		}
		
		$this->module = $module;
	}
	
	public function setAction($action)
	{
		if (!is_string($action) || empty($action))
		{
			throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
		}
		
		$this->action = $action;
	}
	
	public function setView($view)
	{
		if (!is_string($view) || empty($view))
		{
			throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
		}
		
		$this->view = $view;
		
		$this->page->setContentFile(__DIR__.'/../Applications/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
	}
	
	public function unique($value, array $array)
	{
		foreach($array as $id => $val)
		{
			if($val == $value)
			{
				if(is_numeric($value))
				{
					$value ++ ;
				}
				elseif(is_string($value))
				{
					$value = $value.rand(0,10000) ;
				}
				else
				{
					throw new \InvalidArgumentException('Le tableau ne doit contenir que des chiffres et/ou chaine de caractere');	
				}
				
				return $this->unique($value, $array) ;
			}
		}
		
		return $value ;
	}
}