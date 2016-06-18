<?php
namespace Library\Entities;

class Message extends \Library\Entity
{
	protected $technicien,
			  $contenu,
			  $date,
			  $objet,
			  $notification,
			  $statut;

	const TECHNICIEN_INVALIDE = 1;
	const CONTENU_INVALIDE = 2;
	const DATE_INVALIDE = 3;
	const STATUT_INVALIDE = 4;
	const OBJET_INVALIDE = 5;
	const NOTIFICATION_INVALIDE = 6;

  
  // SETTERS //
  
	public function setTechnicien($technicien)
	{
		if (!$this->validator->is_Id($technicien))
		{
			$this->erreurs[] = self::TECHNICIEN_INVALIDE;
		}
		
		$this->technicien = $technicien;
	}
	
	public function setContenu($contenu)
	{
		if (!is_string($contenu))
		{
			$this->erreurs[] = self::CONTENU_INVALIDE;
		}
		
		$this->contenu = $contenu;
	}
	
	public function setObjet($objet)
	{
		if (!is_string($objet) || strlen($objet) > 100)
		{
			$this->erreurs[] = self::OBJET_INVALIDE;
		}
		
		$this->objet = $objet;
	}
	
	public function setStatut($statut)
	{
		if ($statut != 0 && $statut != 1 && $statut != 2)
		{
			$this->erreurs[] = self::STATUT_INVALIDE;
		}
		
		$this->statut = $statut;
	}
	
	public function setDate($date)
	{
		if (!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATE_INVALIDE;
		}
		
		$this->date = $date;
	}
	
	public function setNotification($notification = false)
	{
		if (!$this->validator->is_Id($notification) && $notification != false)
		{
			$this->erreurs[] = self::NOTIFICATION_INVALIDE;
		}
		
		$this->notification = $notification;
	}
  
  // GETTERS //
  
  	public function notification()
	{
		return $this->notification;
	}
  
	public function technicien()
	{
		return $this->technicien;
	}
	
	public function statut()
	{
		return $this->statut;
	}
	
	public function date()
	{
		return $this->date;
	}
	
	public function contenu()
	{
		return $this->contenu;
	}
	
	public function objet()
	{
		return $this->objet;
	}
}