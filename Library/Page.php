<?php
namespace Library;

class Page extends ApplicationComponent
{
	protected $contentFile;
	protected $vars = array();
	protected $layout ;
	
	public function __construct($app, $layout = 'layout')
	{
		 parent::__construct($app);
		 $this->layout = $layout ;
	}
	
	public function addVar($var, $value)
	{
		if (!is_string($var) || is_numeric($var) || empty($var))
		{
			throw new \InvalidArgumentException('Le nom de la variable doit etre une chaine de caractere non nulle');
		}
		
		$this->vars[$var] = $value;
	}
	
	public function getGeneratedPage()
	{
		if (!file_exists($this->contentFile))
		{
			throw new \RuntimeException('La vue specifiee n\'existe pas : '.$this->contentFile);
		}
		
		$user = $this->app->user();
		
		extract($this->vars);
		
		ob_start();
		require $this->contentFile;
		$content = ob_get_clean();
		
		ob_start();
		require __DIR__.'/../Applications/'.$this->app->name().'/Templates/'.$this->layout.'.php';
		
		return ob_get_clean();
	}
	
	public function setContentFile($contentFile)
	{
		if (!is_string($contentFile) || empty($contentFile))
		{
			throw new \InvalidArgumentException('La vue spécifiée est invalide');
		}
		
		$this->contentFile = $contentFile;
	}
}