<?php
namespace Applications\App\Modules\Connexion;

use \Library\Crypt ;
use Library\Form ;

class ConnexionController extends \Library\BackController
{
	public function executeIndex(\Library\HTTPRequest $request)
	{
		if(!$this->user->isAuthenticated())
		{
			$crypt = new Crypt ;
			$this->page->addVar('title', 'Connexion');
			
			$form_connexion = array('connexion' => array('form','form-horizontal form-connexion'),
						  'div login' => array('div','form-group'),
						  	'login' => array('text','Login',array('Login'=>'Login invalide'),'',array('class' => 'form-control')),
						  'fin div login' => array('/div'),
						  'div password' => array('div','form-group'),
						  	'password' => array('password','Mot de passe',array('string'=>'Mot de passe invalide'),'',array('class' => 'form-control')),
						  'fin div password' => array('/div'),
						  'Connexion' => array('submit','connexion','btn btn-default')
						 );
			
			$Form_connexion = new Form(array() , $form_connexion, $this->app, $this->managers) ;
		  
			if ($request->postExists('Connexion'))
			{
				$this->processConnexion($request, $form_connexion) ;
			}
			else
			{
				$this->page->addVar('Connexion_form', $Form_connexion->form());
			}
		}
		else
		{
			$this->app->httpResponse()->redirect('/');
		}
	}
	
	public function executeDeconnexion(\Library\HTTPRequest $request)
	{
		unset($_SESSION['auth']);	
		session_destroy() ;
		$this->app->httpResponse()->redirect('/');
	}
	
	public function processConnexion(\Library\HTTPRequest $request, array $form)
	{
		if(!$this->user->isAuthenticated())
		{
			$login = $request->postData('login');
			$password = $request->postData('password');
			
			$fields = array ('login' => $login, 'password' => $password) ;
			
			$Form_connexion = new Form($fields, $form, $this->app, $this->managers) ;
			
			$erreur = $Form_connexion->bol() ;
			
			if($erreur === false)
			{
				if($erreur == false)
				{
					$crypt = new Crypt ;
					$user = $this->em('User')->DEF->getUnique(array('login' => $login));
					$salt = $user->salt();
					$pass = $crypt->pass($password, $salt, $this->encrypt_key) ;
					
					if($pass == $user->password())
					{
						$this->app->user()->setAuthenticated(true,$statut);
						$this->user->setAttribute('id', $user->id());
						
						$this->app->user()->setAttribute('pseudo', $login) ;
						$this->app->httpResponse()->redirect('/');
					}
					else
					{
						$this->app->user()->setFlash('Identifiants inccorects');
					}
				}
				else
				{
					$this->app->user()->setFlash('Identifiants inccorects');
				}
			}
			
			$this->page->addVar('Connexion_form', $Form_connexion->form());
		}
		else
		{
			$this->app->httpResponse()->redirect('/');
		}
	}
}