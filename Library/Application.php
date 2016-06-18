<?php
namespace Library;
use Library\Session;

abstract class Application
{
	protected $httpRequest;
	protected $httpResponse;
	protected $name;
	protected $user;
	protected $config;
	protected $mode;
	protected $error;
	
	public function __construct()
	{
		$this->httpRequest = new HTTPRequest($this);
		$this->httpResponse = new HTTPResponse($this);
		$this->user = new User($this);
		$this->config = new Config($this);
		$this->error = new Error($this);
		
		$this->mode = $this->config->getGlobal('mode') ;
		$prefix = $this->config->getGlobal('prefix_table') ;
		
		ini_set("display_errors",0);
		error_reporting(0);
		
		define('ENV', $this->mode);
		define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
		define('DISPLAY_ERRORS', TRUE);
		define('ERROR_REPORTING', E_ALL | E_STRICT);
		define('LOG_ERRORS', TRUE);
		define('PREFIX_TABLE', $prefix);
		define('PATH', $this->config->getGlobal('site'));
		
		register_shutdown_function(array($this->error,'shut'));
	
		set_error_handler(array($this->error,'handler'));
		
		if(!date_default_timezone_set($this->config->getGlobal('timezone')))
        {
            date_default_timezone_set('Europe/Paris') ;
        }
		
		$this->name = '';
	}
	
	public function getController()
	{
		$router = new \Library\Router ;
		
		$xml = new \DOMDocument;
		$xml->load(__DIR__.'/../Applications/'.$this->name.'/Config/routes.xml');
		
		$routes = $xml->getElementsByTagName('route');
		
		// On parcourt les routes du fichier XML.
		foreach ($routes as $route)
		{
			$vars = array();
			
			// On regarde si des variables sont présentes dans l'PATH.
			if ($route->hasAttribute('vars'))
			{
				$vars = explode(',',$route->getAttribute('vars'));
			}
			
			$url = $route->getAttribute('url') ;
			
			if(substr($url,0, 1) === '/')
			{
				// On ajoute la route au routeur.
				$url = PATH.$url ;
				$router->addRoute(new Route($url, $route->getAttribute('module'), $route->getAttribute('action'), $vars));
			}
			else
			{
				throw new \RuntimeException('La route n\'est pas valide');
			}
		}
		
		try
		{
		    // On récupère la route correspondante à l'URL.
			
			$parse = explode("/", PATH);
			$subpath = array() ;
			$count = count($parse) ;
			
			if($count > 3)
			{
				for($i = 3 ; $i < $count ; $i++)
				{
					$subpath[] = $parse[$i] ;
				}
			}
			
			$url = PATH ;
			
			foreach($subpath as $sub)
			{
				$url = str_replace("/$sub","",$url);
			}
			
			$url .= $this->httpRequest->requestURI() ;
			
			$matchedRoute = $router->getRoute($url);
		}
		catch (\RuntimeException $e)
		{
			if ($e->getCode() == \Library\Router::NO_ROUTE)
			{
				// Si aucune route ne correspond, c'est que la page demandée n'existe pas.
				$this->httpResponse->redirectError('404');
			}
		}
		
		if(isset($matchedRoute))
		{
			// On ajoute les variables de l'URL au tableau $_GET.
			$_GET = array_merge($_GET, $matchedRoute->vars());
			
			// On instancie le contrôleur.
			$controllerClass = 'Applications\\'.$this->name.'\\Modules\\'.$matchedRoute->module().'\\'.$matchedRoute->module().'Controller';
			return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
		}
		else
		{
			exit() ;	
		}
	}
	
	public function connexionStart()
	{
		$dbname = $this->config()->getGlobal('dbname') ;
		$dblogin = $this->config()->getGlobal('dblogin') ;
		$dbpassword = $this->config()->getGlobal('dbpassword') ;
		$host = $this->config()->getGlobal('host') ;
		
		ini_set('session.save_handler', 'user');

		$session = new Session($host, $dblogin, $dbpassword, $dbname);
		
		session_set_save_handler(array($session, 'open'),
								 array($session, 'close'),
								 array($session, 'read'),
								 array($session, 'write'),
								 array($session, 'destroy'),
								 array($session, 'gc'));
		
		session_start();
		$this->user->setKey() ;
	}
	
	public function run()
	{	
		$this->connexionStart() ;
		
		if($this->mode =='production')
		{
			try
			{
				$controller = $this->getController();
				$controller->execute();
				$this->httpResponse->setPage($controller->page());
				$this->httpResponse->send();
			}
			catch (\InvalidArgumentException $e)
			{
				$this->httpResponse->redirectError();
			}
			catch (\RuntimeException $e)
			{
				$this->httpResponse->redirectError();
			}
			catch (\PDOException $e)
			{
				$this->httpResponse->redirectError();
			}
			catch (\Exception $e)
			{
				$this->httpResponse->redirectError();
			}
			
		}
		elseif($this->mode =='development')
		{
			$controller = $this->getController();
			$controller->execute();
			$this->httpResponse->setPage($controller->page());
			$this->httpResponse->send();
		} 
	}
	
	public function httpRequest()
	{
		return $this->httpRequest;
	}
	
	public function httpResponse()
	{
		return $this->httpResponse;
	}
	
	public function name()
	{
		return $this->name;
	}
	
	public function user()
	{
		return $this->user;
	}
	
	public function config()
	{
		return $this->config;
	}
}