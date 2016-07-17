<?php
namespace Applications\App\Modules\Pack;

use \Library\Entities\Pack;
use \Library\Multiform;
use \Library\Form;

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
		
		if($request->postExists('packs') && !empty($request->postData('packs')))
		{
			$page = $request->postData('packs') ;
			$page = preg_replace(array('/\n/', '/\r/', '/\r\n/'), '#@#', $page);
			$page = explode('#@#', $page);
			$count = 0 ;

			foreach($page as $index => $element)
			{
				if(!empty($element) && preg_match('/\d{4} \d{1,2}:\d{1,2} \t\$\d{1,2}\.\d{10} \t/', $element))
				{
					$element = preg_replace(array('/\t/'), '', $element);
					$element = explode(' ', $element);
					$time = explode(':', $element[3]);
					
					$jour = $element[0];
					$mois = $element[1];
					$annee = $element[2];
					$min = $time[1];
					$heure = $time[0];
					
					$statut = $element[5] ;
					$montant = substr($element[4],1);
					
					$date = new \DateTime("$annee-$mois-$jour $heure:$min:00") ;
					$date->add(new \DateInterval('PT2H'));
					
					if($statut == 'Active')
					{
						$newPack = new Pack (array('date_achat' => $date, 'id_user' => $id_user, 'date' => new \DateTime(), 'montant' => $montant)) ;
						$this->em('Pack')->DEF->save($newPack) ;
						$count ++ ;
					}
				}
			}
			
			$this->user->setFlash("$count Pack(s) ont été ajouté(s) à votre compte");
		}
		
		$packs = $this->em('Pack')->DEF->getList(array('id_user' => $id_user), 'montant', '-1', '-1', 'DESC'); 
		//var_dump($packs);
		$form_pack = array(
			'date_achat' => array('date',"Date d'achat",array('invalide' => "La date d'achat est invalide")),
			'date' => array('date',"Date *",array('invalide' => 'La date est invalide'), '', array('placeholder' => date('d/m/Y H:i:s'))),
			'montant' => array('text',"Montant à l'enregistrement *",array('invalide' => 'Le montant est invalide'), '', array('placeholder' => 0)),
			'enregistrer' => array('submit','Enregistrer')
		);
		
		$Form_pack = new MultiForm($pack, $packs, $form_pack, $this->app, $this->managers) ;
		
		if($request->postExists('enregistrer'))
		{
			$values = array('id_user' => $id_user) ;
			$Form_pack->processMultiform(false, $values) ;
		}
		
		$form_solde = array(
				'solde' => array('text',"",array('invalide' => "Le solde est invalide")),
				'valide_form' => 'Solde mis à jour',
				'save_solde' => array('submit','Enregistrer')
		);
		
		$user = $this->em('User')->DEF->getUnique($id_user);
		
		$Form_solde = new Form($user, $form_solde, $this->app, $this->managers) ;
		
		if($request->postExists('save_solde'))
		{
			$values = array('updated_at' => new \DateTime()) ;
			$Form_solde->processForm($request, false, $values) ;
			$user->setUpdated_at(date('d/m/Y H:i:s'));
		}
		
		$date_solde = $user->updated_at() ;
		
		$this->page->addVar('pack_form', $Form_pack->form());
		$this->page->addVar('solde_form', $Form_solde->form());
		$this->page->addVar('date_solde', $date_solde);
	}
	
	public function executeDelete(\Library\HTTPRequest $request)
	{
		$this->em('Pack')->DEF->delete(array('id_user' => $this->user->getAttribute('id')));
		$this->user->setFlash("Tous les packs de votre compte ont été supprimés");
		$this->app->httpResponse()->redirect('/pack');
	}
}