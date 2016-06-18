<?php
namespace Library;

class HTTPResponse extends ApplicationComponent
{
	protected $page;
	
	public function addHeader($header)
	{
		header($header);
	}
	
	public function redirect($location)
	{			
		header('Location: '.PATH.$location);
		
		exit;
	}
  
  public function redirectError($error = '')
  {
	  $this->page = new Page($this->app);
	  
	  if($error === '')
	  {
		  $this->page->setContentFile(__DIR__.'/../Errors/error.php');
	  }
	  elseif($error === '404')
	  {
		  $this->page->setContentFile(__DIR__.'/../Errors/404.php');
		  $this->addHeader('HTTP/1.0 404 Not Found');
	  }
	  elseif($error === '500')
	  {
		  $this->page->setContentFile(__DIR__.'/../Errors/500.php');
		  $this->addHeader('HTTP/1.1 500 Internal Server Error');
	  }
	  else
	  {
		  $this->page->setContentFile(__DIR__.'/../Errors/'.$error.'.php');
	  }
	  
	  $this->send();
  }
  
	public function send()
	{
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
        {
            exit($this->page->getGeneratedPage());
        }
	}
  
	public function setPage(Page $page)
	{
		$this->page = $page;
	}
	
	// Changement par rapport à la fonction setcookie() : le dernier argument est par défaut à true.
	public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
	{
		setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}
}