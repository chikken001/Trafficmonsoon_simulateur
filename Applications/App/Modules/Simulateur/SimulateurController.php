<?php
namespace Applications\App\Modules\Simulateur;

class SimulateurController extends \Library\BackController
{
	public function __construct($app, $module, $action)
	{
		parent::__construct($app, $module, $action);
			
		if(!$this->user->isAuthenticated())
		{
			$this->app->httpResponse()->redirect('/connexion');
		}
	}
	
	public function executeIndex(\Library\HTTPRequest $request)
	{
		$this->page->addVar('title', 'Traffic-Monsoon Simulateur');
	}
	
	public function executeRedirect500(\Library\HTTPRequest $request)
	{
		$this->app->httpResponse()->redirectError('500');
	}
}