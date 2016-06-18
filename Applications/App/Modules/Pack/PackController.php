<?php
namespace Applications\App\Modules\Pack;

use \Library\Entities\Pack;
use \Library\Multiform;

class PackController extends \Library\BackController
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
		$this->page->addVar('title', 'TM Simulateur - Packs');
		$id_user = $this->user->getAttribute('id') ;
		
		$pack = new Pack() ;
		$packs = $this->em('Pack')->DEF->getList(array('id_user' => $id_user)); 
		
		$form_pack = array(
			'date_achat' => array('date',"Date d'achat *",array('invalide' => 'La date est invalide')),
			'enregistrer' => array('submit','Enregistrer')
		);
		
		$Form_pack = new MultiForm($pack, $packs, $form_pack, $this->app, $this->managers, 'Pack') ;
		
		if($request->postExists('enregistrer'))
		{
			$values = array('id_user' => $id_user) ;
			$Form_pack->processMultiform(false, $values) ;
		}
		
		$this->page->addVar('pack_form', $Form_pack->form());
	}
}