<?php
namespace Library\Entities;

class Communication extends \Library\Entity
{
	protected $mode,
			  $message,
			  $contact,
			  $notification,
			  $date;
	
	const MODE_INVALIDE = 1;
	const MESSAGE_INVALIDE = 2;
	const ROLE_INVALIDE = 3;
	const CONTACT_INVALIDE = 4;
	const NOTIFICATION_INVALIDE = 5;
	const DATE_INVALIDE = 6;
  
  // SETTERS //
	
	public function setMode($mode)
	{
		$modes = array('sms', 'oral', 'téléphone', 'mail') ;
		
		if(!in_array($mode, $modes))
		{
			$this->erreurs[] = self::MODE_INVALIDE;
		}
		
		$this->mode = $mode;
	}
	
	public function setNotification($notification)
	{
		if (!$this->validator->is_Id($notification))
		{
			$this->erreurs[] = self::notification_INVALIDE;
		}
		
		$this->notification = $notification;
	}
	
	public function setContact($contact)
	{
		if (!$this->validator->is_Id($contact))
		{
			$this->erreurs[] = self::CONTACT_INVALIDE;
		}
		
		$this->contact = $contact;
	}
	
	public function setMessage($message)
	{
		if (!is_string($message))
		{
			$this->erreurs[] = self::MESSAGE_INVALIDE;
		}
		
		$this->message = $message;
	}
	
	public function setDate($date)
	{
		if (!$this->validator->is_Date($date, 'datetime'))
		{
			$this->erreurs[] = self::DATE_INVALIDE;
		}
		
		$this->date = $date;
	}
	
  
  // GETTERS //
  
	public function date()
	{
		return $this->date;
	}
	
	public function message()
	{
		return $this->message;
	}
	
	public function contact()
	{
		return $this->contact;
	}	
	
	public function notification()
	{
		return $this->notification;
	}
	
	public function mode()
	{
		return $this->mode;
	}
}