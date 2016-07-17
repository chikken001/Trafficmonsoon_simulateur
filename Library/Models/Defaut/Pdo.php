<?php
namespace Library\Models\Defaut;

class Pdo
{
	protected $manager,
			  $namespace,
			  $select,
			  $vars,
			  $binds,
			  $types,
			  $name;
	
	public function __construct(\Library\Manager $manager)
	{
		$this->manager = $manager ;
		
		$RC = new \ReflectionClass($manager);
		$namespace = $RC->getName();

		$entity = explode("\\", $namespace);
		$name = $entity[count($entity)-1];
		
		if(substr($name, -11, 11) === 'Manager_PDO')
		{
			$name = substr($name, 0, -11);
		}
		else
		{
			$debug = debug_backtrace();
			throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Le manager doit etre un manager PDO : '.$name);
		}
		
		$requete = $this->manager->dao->query("SHOW TABLES LIKE '".$this->manager->database()."'");
		$res = $requete->fetchColumn() ;
		$requete->closeCursor();
		
		if(empty($res))
		{
			throw new \InvalidArgumentException('La table : '.$this->manager->database().' n\'existe pas');
		}
		
		$requete = $this->manager->dao->query('DESCRIBE '.$this->manager->database());
		$champs = $requete->fetchAll();
		$requete->closeCursor();
		
		$types = array() ;
		
		foreach($champs as $champ)
		{
			$types[$champ['Field']] = $champ['Type'] ;
		}
		
		$this->namespace = '\Library\Entities\\'.$name ;
		$entity = new $this->namespace ; 
		$attributs = $entity->getVars();
		$select = 'SELECT ' ;
		$binds = '' ;
		$i = 0 ;
		
		foreach ($attributs as $nom => $valeur) 
		{
			if($nom != 'validator' && $nom != 'erreurs')
			{
				if(!isset($types[$nom]))
				{
					throw new \InvalidArgumentException('La colonne '.$nom.' n\'existe pas dans la table : '.$this->manager->database());
				}
				
				$select .= $nom ;
				$binds .= "$nom = :$nom" ;
				$this->vars[] = $nom ;
				
				if($i == count($attributs)-3)
				{
					$select .= ' ' ;
					$binds .= ' ' ;
				}
				else
				{
					$select .= ', ' ;
					$binds .= ', ' ;
				}
				
				$i ++;
			}
		}
		
		$select .= 'FROM '.$this->manager->database() ;
		$this->select = $select ;
		$this->binds = $binds ;
		$this->types = $types ;
		$this->name = strtolower($name) ;
	}
	
	public function save($entity)
	{
		if($entity instanceof $this->namespace)
		{
			if ($entity->isValid())
			{
				$entity->isNew() ? $this->add($entity) : $this->modify($entity);
			}
			else
			{
				throw new \RuntimeException('L\'entite '.$this->namespace.' doit etre valide pour etre enregistre');
			}
		}
		else
		{
			$debug = debug_backtrace();
			throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : l\'entite doit etre une instance de '.$this->namespace.' pour Pdo::save');
		}
	}
	
	public function getUnique($arg = false, $exc = false, $or = false, $format_date = true)
	{
		$sql = $this->select.' WHERE' ;
		$bind = array() ;
		$first = true ;
		
		if(is_array($arg))
		{
			foreach($arg as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' ' : $sql .= ' AND ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					$first = false ;
				}
			}
		}
		elseif(preg_match('/^[\d]+$/',$arg))
		{
			$arg = intval($arg) ;
			$sql .= ' id = :id' ;
			$first = false ;
			$bind[':id'] = $arg ;
		}
		
		if(is_array($or))
		{
			foreach($or as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' ' : $sql .= ' OR ' ;
					
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if(is_array($exc))
		{
			foreach($exc as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' ' : $sql .= ' AND ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument != :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NOT NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if($first == true)
		{
			$debug = debug_backtrace();
			throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Argument invalide passe a Pdo::getUnique');
		}
		
		$requete = $this->manager->dao->prepare($sql);
		
		foreach ($bind as $nom => $valeur) 
		{
			$requete->bindValue($nom, $valeur);
		}
		
		$requete->execute();
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->namespace);
		
		$entity = $requete->fetch();
		
		$requete->closeCursor();
		
		if(!empty($entity) && (in_array('date', $this->types) || in_array('datetime', $this->types)) && $format_date == true)
		{
			foreach ($this->types as $argument => $type)
			{
				if($type == 'date' && $entity[$argument] != '0000-00-00')
				{
					$method = 'set'.ucfirst($argument) ;
					$entity->$method(date_format(date_create($entity[$argument]),'d/m/Y')) ;
				}
				elseif($type == 'datetime' && $entity[$argument] != '0000-00-00')
				{
					$method = 'set'.ucfirst($argument) ;
					$entity->$method(date_format(date_create($entity[$argument]),'d/m/Y H:i:s')) ;
				}
			}
		}
		
		return $entity;
	}
	
	public function count($arg = false, $exc = false, $or = false)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->manager->database() ;
		$bind = array() ;
		
		if(!is_array($arg) && !is_array($exc))
		{
			return $this->manager->dao->query($sql)->fetchColumn();
		}
		
		$first = true ;
		
		if(is_array($arg))
		{
			foreach($arg as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' AND ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if(is_array($or))
		{
			foreach($or as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' OR ' ;
					
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if(is_array($exc))
		{
			foreach($exc as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' AND ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument != :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NOT NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		$requete = $this->manager->dao->prepare($sql);
		
		foreach ($bind as $nom => $valeur) 
		{
			$requete->bindValue($nom, $valeur);
		}
		
		$requete->execute();
		
		$nombre = $requete->fetchColumn() ;
		
		$requete->closeCursor();
		
		return $nombre ;
	}
	
	public function getList($arg = false, $order ='id', $debut = -1, $limite = -1, $order_option = 'ASC', $exc = false, $or = false, $format_date = true)
	{
		$sql = $this->select ;
		$order_option = strtoupper((string)$order_option) ;
		$bind = array() ;
		$first = true ;
		
		if(is_array($arg))
		{
			foreach($arg as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' AND ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if(is_array($or))
		{
			foreach($or as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' OR ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if(is_array($exc))
		{
			foreach($exc as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' AND ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument != :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NOT NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if($order_option != 'ASC' && $order_option != 'DESC')
		{
			$order_option = 'ASC' ;
		}
		
		if($order != 'id' && !in_array($order, $this->vars))
		{		
			$order ='id' ;
		}
		
		$sql .= " ORDER BY $order $order_option" ;
		
		$debut = intval($debut) ;
		$limite = intval($limite) ;
		
		if ($debut != -1 || $limite != -1)
		{
			$sql .= ' LIMIT '.$limite.' OFFSET '.$debut;
		}
		
		$requete = $this->manager->dao->prepare($sql);
		
		foreach ($bind as $nom => $valeur) 
		{
			$requete->bindValue($nom, $valeur);
		}
		
		$requete->execute();
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->namespace);
		
		$listeEntities = $requete->fetchAll();
		
		$requete->closeCursor();
		
		if(in_array('date', $this->types) || in_array('datetime', $this->types) && $format_date == true)
		{
			foreach ($listeEntities as $entity)
			{
				foreach ($this->types as $argument => $type)
				{
					if($type == 'date' && $entity[$argument] != '0000-00-00' && !empty($entity[$argument]) && !preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4}$)/',$entity[$argument]))
					{
						$method = 'set'.ucfirst($argument) ;
						$entity->$method(date_format(date_create($entity[$argument]),'d/m/Y')) ;
					}
					elseif($type == 'datetime' && $entity[$argument] != '0000-00-00' && !empty($entity[$argument]) && !preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}$)/',$entity[$argument]))
					{
						$method = 'set'.ucfirst($argument) ;
						$entity->$method(date_format(date_create($entity[$argument]),'d/m/Y H:i:s')) ;
					}
				}
			}
		}
		
		return $listeEntities ;
	}
	
	public function modify($entity)
	{
		if($entity instanceof $this->namespace && $entity->id() !== null)
		{
			$sql = 'UPDATE '.$this->manager->database().' SET ' ;
			$first = true ;
			$bind = array () ;
			
			$attributs = $entity->getVars();

			foreach ($attributs as $nom => $valeur) 
			{
				if($nom  != 'erreurs' && $nom  != 'id' && $nom  != 'validator' && isset($this->types[$nom]))
				{
					if($this->types[$nom] == 'date')
					{
						if(!$valeur instanceof \DateTime)
						{
							date_parse_from_format("Y-m-d", $valeur)['error_count'] != 0 ? $valeur = $this->manager->date_format($valeur) : $valeur = $valeur ;
						}
						else
						{
							$valeur = $valeur->format('Y-m-d');
						}
					}
					elseif($this->types[$nom] == 'datetime')
					{							
						if(!$valeur instanceof \DateTime)
						{
							date_parse_from_format("Y-m-d H:i:s", $valeur)['error_count'] != 0 ? $valeur = $this->manager->datetime_format($valeur) : $valeur = $valeur ;
						}
						else
						{
							$valeur = $valeur->format('Y-m-d H:i:s');
						}
					}
					
					$first == false ? $sql .= ', ' : $first = false ;
					$sql .= $nom.' = :'.$nom ;
					$bind[':'.$nom] = $valeur ;
				}
			}
			
			$sql .= ' WHERE id = :id' ;
			
			$requete = $this->manager->dao->prepare($sql) ;

			foreach ($bind as $nom => $valeur) 
			{
				$valeur === false ? $requete->bindValue($nom, null, \PDO::PARAM_INT) : $requete->bindValue($nom, $valeur);
			}
			
			$requete->bindValue(':id', $entity->id(), \PDO::PARAM_INT);
			
			$requete->execute();
			$requete->closeCursor();
		}
		else
		{
			throw new \InvalidArgumentException('l\'entite doit avoir un id et etre une instance de : '.$this->namespace.' pour Pdo::modify');
		}
	}
	
	public function add($entity)
	{
		if($entity instanceof $this->namespace)
		{
			$requete = $this->manager->dao->prepare('INSERT INTO '.$this->manager->database().' SET '.$this->binds) ;
			
			foreach ($this->vars as $argument)
			{
				$valeur = $entity->$argument() ;
				
				if($this->types[$argument] == 'date' || $this->types[$argument] == 'datetime')
				{
					if(!$valeur instanceof \DateTime)
					{
						if($this->types[$argument] == 'date')
						{
							date_parse_from_format("Y-m-d", $valeur)['error_count'] != 0 ? $valeur = $this->manager->date_format($valeur) : $valeur = $valeur ;
						}
						elseif($this->types[$argument] == 'datetime')
						{
							date_parse_from_format("Y-m-d H:i:s", $valeur)['error_count'] != 0 ? $valeur = $this->manager->datetime_format($valeur) : $valeur = $valeur ;
						}
					}
					else 
					{
						$this->types[$argument] == 'date' ? $format = "Y-m-d" : $format = "Y-m-d H:i:s" ; 
						$valeur = $valeur->format($format) ;
					}
				}
				
				$valeur === false ? $requete->bindValue(":$argument", null, \PDO::PARAM_INT) : $requete->bindValue(":$argument", $valeur) ;
			}
			
			$requete->execute();
			$requete->closeCursor();
		}
		else
		{
			$debug = debug_backtrace();
			throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : l\'entite doit etre une instance de : '.$this->namespace.' pour Pdo::add');
		}
	}
	
	public function delete($arg = false, $exc = false, $or = false)
	{
		$sql = 'DELETE FROM '.$this->manager->database().' WHERE' ;
		$bind = array() ;
		$first = true ;
		
		if(is_array($arg))
		{
			foreach($arg as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' ' : $sql .= ' AND ' ;
					
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					
					$first = false ;
				}
			}
		}
		elseif(ctype_digit($arg) || is_int($arg))
		{
			$arg = intval($arg) ;
			$first = false ;
			$sql .= ' id = :id' ;
			$bind[':id'] = $arg ;
		}
		
		if(is_array($or))
		{
			foreach($or as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' OR ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument = :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if(is_array($exc))
		{
			foreach($exc as $argument => $valeur)
			{
				if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
				{
					$first === true ? $sql .= ' WHERE ' : $sql .= ' AND ' ;
					if($valeur !== 'NULL')
					{
						$sql .= "$argument != :$argument";
						$bind[':'.$argument] = $valeur ;
					}
					else
					{
						$sql .= "$argument IS NOT NULL" ;
					}
					$first = false ;
				}
			}
		}
		
		if($first == true)
		{
			$debug = debug_backtrace();
			throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Argument invalide passe a Pdo::delete');
		}
		
		$requete = $this->manager->dao->prepare($sql);
		
		foreach ($bind as $nom => $valeur) 
		{
			$requete->bindValue($nom, $valeur);
		}
		
		$requete->execute();
		
		$requete->closeCursor();
	}
	
	public function get($arg, $id)
	{
		$requete = $this->manager->dao->query('SELECT '.(string)$arg.' FROM '.$this->manager->database().' WHERE id ='.(int)$id) ;
		
		$value = $requete->fetch() ;
		
		$requete->closeCursor();
		
		return $value[$arg] ;
	}
	
	public function update(array $set, array $arg = array(), array $exc = array(), array $or = array())
	{
		$first = true ;
		$sql = 'UPDATE '.$this->manager->database().' SET ' ;
		$bind = array() ;
		
		foreach($set as $argument => $valeur)
		{
			if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
			{
				$first == false ? $sql .= ', ' : $first = false ;
				
				if($valeur !== 'NULL')
				{
					if($this->types[$argument] == 'date')
					{
						date_parse_from_format("Y-m-d", $valeur)['error_count'] != 0 ? $valeur = $this->manager->date_format($valeur) : $valeur = $valeur ;
					}
					elseif($this->types[$argument] == 'datetime')
					{
						date_parse_from_format("Y-m-d H:i:s", $valeur)['error_count'] != 0 ? $valeur = $this->manager->datetime_format($valeur) : $valeur = $valeur ;
					}
					
					$sql .= $argument.' = :'.$argument ;
					$bind[':'.$argument] = $valeur ;
				}
				else
				{
					$sql .= "$argument = NULL" ;
				}
			}
		}
		
		$first = true ;

		foreach($arg as $argument => $valeur)
		{
			if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
			{
				$first === true ? $sql .= ' WHERE ' : $sql .= ' AND ' ;
				
				if($valeur !== 'NULL')
				{
					$sql .= "$argument = :w$argument";
					$bind[':w'.$argument] = $valeur ;
				}
				else
				{
					$sql .= "$argument IS NULL" ;
				}
				
				$first = false ;
			}
		}
		
		foreach($or as $argument => $valeur)
		{
			if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
			{
				$first === true ? $sql .= ' WHERE ' : $sql .= ' OR ' ;
				if($valeur !== 'NULL')
				{
					$sql .= "$argument = :w$argument";
					$bind[':w'.$argument] = $valeur ;
				}
				else
				{
					$sql .= "$argument IS NULL" ;
				}
				$first = false ;
			}
		}
		
		foreach($exc as $argument => $valeur)
		{
			if(in_array($argument, $this->vars) && (is_string($valeur) || is_numeric($valeur)))
			{
				$first === true ? $sql .= ' WHERE ' : $sql .= ' AND ' ;
				if($valeur !== 'NULL')
				{
					$sql .= "$argument != :w$argument";
					$bind[':w'.$argument] = $valeur ;
				}
				else
				{
					$sql .= "$argument IS NOT NULL" ;
				}
				$first = false ;
			}
		}
		
		$requete = $this->manager->dao->prepare($sql);
		
		foreach ($bind as $nom => $valeur) 
		{
			$requete->bindValue($nom, $valeur);
		}
		
		$requete->execute();
		
		$requete->closeCursor();
	}
	
	public function associate($id, $id_associate, $entity_associate, $args = false)
	{
		if(empty($id) || $id == 0)
		{
			$debug = debug_backtrace();
			throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : l\'id de "'.$this->name.'" est invalide');
		}
		
		$sql = 'SELECT COUNT(*) FROM '.$this->manager->database().'_'.ucfirst((string)$entity_associate).' WHERE '.$this->name.' = '.(int)$id.' AND '.strtolower((string)$entity_associate).' = '.(int)$id_associate ;
		
		$requete = $this->manager->dao->query($sql) ;
		$nb = $requete->fetch();
		
		if($nb[0] == 0)
		{
			$sql = 'INSERT INTO '.$this->manager->database().'_'.ucfirst((string)$entity_associate).' SET '.$this->name.' = :'.$this->name.', '.strtolower((string)$entity_associate).' = :'.strtolower((string)$entity_associate) ;
			
			if(is_array($args))
			{
				$bind = array() ;
				
				foreach($args as $arg => $value)
				{
					$sql .= ', '.$arg.' = :'.$arg ;
					$bind[':'.$arg] = $value ;
				}
			}
			
			$requete = $this->manager->dao->prepare($sql);
			
			if(isset($bind))
			{
				foreach ($bind as $nom => $valeur) 
				{
					$requete->bindValue($nom, $valeur);
				}
			}
			
			$requete->bindValue(':'.$this->name, $id, \PDO::PARAM_INT);
			$requete->bindValue(':'.strtolower((string)$entity_associate), $id_associate, \PDO::PARAM_INT);
			
			$requete->execute();
			$requete->closeCursor();
		}
	}
	
	public function getAssociate($id, $target, array $arg = array(), array $exc = array(), array $or = array())
	{
		$target = strtolower($target) ;
		$bind = array() ;
		
		$sql = 'SELECT * FROM '.$this->manager->database().'_'.ucfirst((string)$target).' WHERE '.$this->name.' = '.(int)$id ;
		
		foreach($arg as $argument => $valeur)
		{
			if(is_string($valeur) || is_numeric($valeur))
			{
				$sql .= ' AND ' ;
				
				if($valeur !== 'NULL')
				{
					$sql .= "$argument = :$argument";
					$bind[':'.$argument] = $valeur ;
				}
				else
				{
					$sql .= "$argument IS NULL" ;
				}
				
				$first = false ;
			}
		}
		
		foreach($or as $argument => $valeur)
		{
			if(is_string($valeur) || is_numeric($valeur))
			{
				$sql .= ' OR ' ;
				if($valeur !== 'NULL')
				{
					$sql .= "$argument = :$argument";
					$bind[':'.$argument] = $valeur ;
				}
				else
				{
					$sql .= "$argument IS NULL" ;
				}
				$first = false ;
			}
		}
		
		foreach($exc as $argument => $valeur)
		{
			if(is_string($valeur) || is_numeric($valeur))
			{
				$sql .= ' AND ' ;
				if($valeur !== 'NULL')
				{
					$sql .= "$argument != :$argument";
					$bind[':'.$argument] = $valeur ;
				}
				else
				{
					$sql .= "$argument IS NOT NULL" ;
				}
				$first = false ;
			}
		}
		
		$requete = $this->manager->dao->prepare($sql);
		
		foreach ($bind as $nom => $valeur) 
		{
			$requete->bindValue($nom, $valeur);
		}
		
		$requete->execute();
		
		$listeAssociate = $requete->fetchAll();
		
		$requete->closeCursor();
		
		return $listeAssociate ;
	}
	
	public function deleteAssociate($id, $id_associate, $target)
	{
		$this->manager->dao->exec('DELETE FROM '.$this->manager->database().'_'.ucfirst((string)$target).' WHERE '.strtolower((string)$target).' = '.(int)$id_associate.' AND '.$this->name.' = '.(int)$id) ;
	}
	
	public function getArgAssociate($arg, $id_target, $target, $id)
	{
		$database = $this->manager->database().'_'.ucfirst((string)$target) ;
		$target = strtolower($target) ;
		
		$sql = 'SELECT '.(string)$arg.' FROM '.$database.' WHERE '.$target.' = '.(int)$id_target.' AND '.$this->name.' = '.(int)$id ;	
		
		$requete = $this->manager->dao->query($sql) ;
		$argument = $requete->fetch();
		
		$requete->closeCursor();
		
		return $argument[$arg] ;
	}
	
	public function updateArgAssociate($arg, $value, $id_target, $target, $id)
	{
		if(!empty($this->getAssociate($id, $target, $args = array($target => $id_target))))
		{
			$sql = 'UPDATE '.$this->manager->database().'_'.ucfirst((string)$target).' SET '.(string)$arg.' = "'.$value.'" WHERE '.strtolower((string)$target).' = '.(int)$id_target.' AND '.$this->name.' = '.(int)$id ;
			$this->manager->dao->exec($sql) ;
		}
		else
		{
			 $this->associate($id, $id_target, $target, $args = array($arg => $value)) ;
		}
	}
}
