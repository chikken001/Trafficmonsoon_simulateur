<?php
namespace Library;

abstract class Manager
{
	public $dao;
	public $DEF ;
	protected $entity_database ;
	
	public function __construct($dao)
	{
		$this->dao = $dao;
	}
	
	public function date_format ($date)
	{
		if(date_parse_from_format("Y-m-d", $date)['error_count'] != 0 && !empty($date))
		{
			$date = explode('/', $date) ;
			
			if(isset($date['0']) && isset($date['1']) && isset($date['2']))
			{
				$jour = $date['0'] ;
				$anne = $date['2'] ;
				$mois = $date['1'] ;
				
				$date = new \DateTime("$anne-$mois-$jour") ;
				$date = $date->format('Y-m-d');
			}
			else
			{
				return false ;	
			}
		}
		
		return $date ;
	}
	
	public function datetime_format ($date)
	{
		if(date_parse_from_format("Y-m-d H:i:s", $date)['error_count'] != 0 && !empty($date))
		{
			$date = explode('/', $date) ;
			
			if(isset($date['0']) && isset($date['1']) && isset($date['2']))
			{
				$jour = $date['0'] ;
				$mois = $date['1'] ;
				$part = $date['2'] ;
				
				$part = explode(' ', $part) ;
				$anne = $part['0'] ;
				$hour = $part['1'] ;
				
				$hour = explode(':', $hour) ;
				$heure = $hour['0'] ;
				$min = $hour['1'] ;
				
				$date = new \DateTime("$anne-$mois-$jour $heure:$min:00") ;
				$date = $date->format('Y-m-d H:i:s');
			}
			else
			{
				return false ;	
			}
		}
		
		return $date ;
	}
	
	public function database()
	{
		return $this->entity_database ;
	}
}