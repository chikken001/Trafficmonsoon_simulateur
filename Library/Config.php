<?php
namespace Library;
class Config extends ApplicationComponent
{
	protected $vars = array();
	protected $varsGlobal = array();
  
	public function get($var)
	{
		if (!$this->vars)
		{
			$xml = new \DOMDocument;
			$xml->load(__DIR__.'/../Applications/'.$this->app->name().'/Config/app.xml');
			
			$elements = $xml->getElementsByTagName('define');
			
			foreach ($elements as $element)
			{
				$this->vars[$element->getAttribute('var')] = $element->getAttribute('value');
			}
		}
	
		if (isset($this->vars[$var]))
		{
			return $this->vars[$var];
		}
		
		return null;
	}
		
	public function getGlobal($var)
	{
		if (!$this->varsGlobal)
		{
			$xml = new \DOMDocument;
			$xml->load(__DIR__.'/../Applications/Config/app.xml');
			
			$elements = $xml->getElementsByTagName('define');
			
			foreach ($elements as $element)
			{
				$this->varsGlobal[$element->getAttribute('var')] = $element->getAttribute('value');
			}
		}
		
		if (isset($this->varsGlobal[$var]))
		{
			return $this->varsGlobal[$var];
		}
		
		return null;
	}
}