<?php
namespace Library;

class Paginator
{
	protected $space = '...' ;
	
	public function paginator(\Library\Manager $manager, $nb, \Library\HTTPRequest $request, $arg = false, $order ='id', $order_option = 'ASC', $exc = false, $or = false)
	{
		if(!is_int($nb) || (!is_array($arg) && $arg !== false))
		{
			throw new \InvalidArgumentException('$nb doit etre un entier et $arg un tableaux, defini a false, ou non defini');	
		}
		
		$nbentity = $manager->DEF->count($arg)  ;
		$nbentity > $nb ? $pages = ceil($nbentity / $nb) : $pages = 1 ;
		$this->url = $request->requestURI() ;
		
		if (!$request->postExists('page'))
		{
			$entities = $manager->DEF->getList($arg, $order, 0, $nb, $order_option, $exc, $or)  ;
			$page = 1 ;
		}
		else
		{
			$page = $request->postData('page') ;
		
			$debut = $page * $nb - $nb ;
			
			$entities = $manager->DEF->getList($arg, $order, $debut, $nb, $order_option, $exc, $or)  ;
		}
		
		if($pages > 1)
		{	
			$precedent =  $page - 1 ;
			$suivant = $page + 1 ;
			
			if($page == 1)
			{
				if($suivant == $pages)
				{
					$paginator = $this->page($page, 1).$this->page($pages) ;
				}
				elseif($suivant +1 != $pages)
				{
					$paginator = $this->page($page, 1).$this->page($suivant).$this->space.$this->page($pages) ;
				}
				else
				{
					$paginator = $this->page($page, 1).$this->page($suivant).$this->page($pages) ;
				}
			}
			elseif($page == $pages)
			{
				if($precedent == 1)
				{
					$paginator = $this->page($precedent).$this->page($pages, 1) ;
				}
				elseif($precedent - 1 != 1)
				{
					$paginator = $this->page(1).$this->space.$this->page($precedent).$this->page($pages, 1) ;
				}
				else
				{
					$paginator = $this->page(1).$this->page($precedent).$this->page($pages, 1) ;
				}
			}
			else
			{
				if($precedent == 1 && $suivant != $pages && $suivant + 1 != $pages)
				{
					$paginator = $this->page($precedent).$this->page($page, 1).$this->page($suivant).$this->space.$this->page($pages) ;
				}
				elseif($precedent == 1 && $suivant + 1 == $pages)
				{
					$paginator = $this->page($precedent).$this->page($page, 1).$this->page($suivant).$this->page($pages) ;
				}
				elseif($precedent == 1 && $suivant == $pages)
				{
					$paginator = $this->page($precedent).$this->page($page, 1).$this->page($suivant) ;
				}
				elseif($suivant == $pages && $precedent -1 != 1)
				{
					$paginator = $this->page(1).$this->space.$this->page($precedent).$this->page($page, 1).$this->page($pages) ;
				}
				elseif($suivant == $pages)
				{
					$paginator = $this->page(1).$this->page($precedent).$this->page($page, 1).$this->page($pages) ;
				}
				else
				{
					$paginator = $this->page(1) ;
					
					if($precedent > 2){ $paginator.= $this->space ;}
					
					$paginator .= $this->page($precedent).$this->page($page, 1).$this->page($suivant) ;
					
					if($suivant + 1 != $pages){$paginator.= $this->space ;} 
					
					$paginator .= $this->page($pages) ;
				}
			}
			
			$paginator = "<span class='paginator'>".$paginator."</span>" ;
		}
		else
		{
			$paginator = '';	
		}
		
		return array ($paginator, $entities) ;
	}
	
	private function page($page, $active = 0)
	{
		if($active == 1)
		{
			return "<span class='page_active'>$page</span>" ;
		}
		else
		{
			$parse = explode("/", PATH);
			$subpath = array() ;
			$count = count($parse) ;
			
			if($count > 3)
			{
				for($i = 3 ; $i < $count ; $i++)
				{
					$subpath[] = $parse[$i] ;
				}
			}
			
			$url = PATH ;
			
			foreach($subpath as $sub)
			{
				$url = str_replace("/$sub","",$url);
			}
			
			$url .= $_SERVER['REQUEST_URI'] ;
			
			return "<form action=".$url." method='post'><input type='submit' value='$page' name='page' /></form>" ;
		}
	}
}