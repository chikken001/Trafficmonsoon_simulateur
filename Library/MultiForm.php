<?php
namespace Library;

use \Library\Crypt ;

class MultiForm
{
	protected $crypt ;
	protected $nom ;
	protected $class ;
	protected $objet ;
	protected $token ;
	protected $managers ;
	protected $app ;
	protected $namespace ;
	protected $fields ;
	protected $entities = array() ;
	protected $class_form ;
	protected $id_form ;
	protected $class_bouton ;
	protected $entity ;
	protected $new_erreurs = array() ;
	protected $erreurs = array() ;
	protected $valide = '' ;
	protected $vars = array() ;
	protected $count = 1 ;
	protected $multi = array() ;
	protected $listes_multiform = array();
	protected $files_multiform = array();
	protected $adds_fields = array() ;
	protected $adds = array() ;
	protected $form_file = '' ;
	protected $id = '' ;
	
	public function __construct($entity, $entities, array $fields, \Library\Application $app, \Library\Managers $managers, $name = '', $class = '', $class_form = '', $class_bouton ='')
	{
		$this->crypt = new Crypt ;
		$this->id = uniqid() ;
		$listes_multiform = array() ;
		$files_multiform = array() ;
		$this->managers = $managers ;
		$types = array('form','text','textarea','submit','button','hidden','tel','email','password','date','color','datetime','datetime-local','month','number','range','search','time','url','week','multiple','liste','radio','file') ;
		$attributes = array('accesskey','class','contenteditable','contextmenu','dir','draggable','dropzone','hidden','id','lang','spellcheck','style','tabindex','title','translate','placeholder') ;
		
		if(is_object($entity))
		{
			$this->objet = $entity ;
			$RC = new \ReflectionClass($entity);
			$this->namespace = $RC->getName();
	
			$entity = explode("\\", $this->namespace);
			$this->entity = $entity[count($entity)-1];
			
			if($name != '')
			{
				if(!is_string($name))
				{
					throw new \InvalidArgumentException('Le nom n\'est pas valide');	
				}
	
				$this->nom = $name ;
			}
			else
			{
				$this->nom = $this->entity ;
			}
		}
		else
		{
			throw new \InvalidArgumentException('$entity doit etre un objet');
		}
		
		if(is_array($entities))
		{
			if(count($entities) > 0)
			{
				foreach($entities as $entity)
				{
					if(!$entity instanceof $this->namespace)
					{
						throw new \InvalidArgumentException('Le tableau d\'entite contient une instance invalide ou différente de : '.$this->entity);
					}
				}
			}
			
			$this->entities = $entities ;
		}
		elseif(!empty($entities))
		{
			throw new \InvalidArgumentException('$entities doit etre un tableau ou etre vide');
		}
		
		$entity = new $this->namespace ; 
		
		$attributs = $entity->getVars();
			
		foreach ($attributs as $nom => $valeur) 
		{
			$this->vars[] = $nom ;
		}
		
		foreach($fields as $field => $conf)
		{
			if(is_string($field) && $conf[0] != 'div' && $conf[0] != '/div' && $conf[0] != 'p' && $conf[0] != 'h')
			{
				if($field != 'empty_form' && $field != 'valide_form')
				{
					if(in_array($conf[0], $types))
					{
						if($conf[0] != 'hidden' && $conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form' && $conf[0] != 'multiple' && $conf[0] != 'liste' && $conf[0] != 'radio')
						{
							if(($conf[0] != 'textarea' && isset($conf[3]) && is_object($conf[3])) || ($conf[0] == 'textarea' && isset($conf[5]) && is_object($conf[5])))
							{
								$this->adds_fields[] = $field ;	
							}
							elseif(!in_array($field, $this->vars))
							{
								throw new \InvalidArgumentException('Le champ "'.$field.'" specifie dans $fields n\'est pas un attribut de l\'objet : '.$this->entity);
							}

							if(isset($conf[1]) && !is_string($conf[1]) && !empty($conf[1]))
							{
								throw new \InvalidArgumentException('Le Label specifie a '.$field.' est invalide : '.$conf[1]);
							}
							
							if(isset($conf[2]) && !is_array($conf[2]) && !empty($conf[2]))
							{
								throw new \InvalidArgumentException('La variable contenant les messages d\'erreurs specifie a '.$field.' doit etre un tableau valide');
							}
							else
							{
								foreach($conf[2] as $error => $msg)
								{
									$const = strtoupper($field.'_'.$error) ;
									
									if(($conf[0] != 'textarea' && isset($conf[3]) && is_object($conf[3])) || ($conf[0] == 'textarea' && isset($conf[5]) && is_object($conf[5])))
									{
										$validator = new Validator ;
										
										if($error != 'int' && $error != 'string' && $error != 'empty' && !preg_match('/^regex\(/', $error))
										{
											$method = 'is_'.ucfirst($error);
											
											if(!is_callable(array($validator, $method)))
											{
												throw new \InvalidArgumentException('Le validator ne contient pas la methode : '.$method.' pour "'.$field.'"');
											}
										}
										elseif(preg_match('/^regex\(/', $error) && $error[strlen($error)-1] !== ')')
										{
											throw new \InvalidArgumentException('L\'appel de la regex doit etre sous la forme : regex(REGEX) pour "'.$field.'"');
										}
									}
									elseif(!defined($this->namespace."::$const"))
									{
										throw new \InvalidArgumentException('L\'erreur specifie a "'.$field.'" ('.$const.') n\'est pas defini a l\'entite : '.$this->entity);
									}
									
									if(!isset($msg) || !is_string($msg))
									{
										throw new \InvalidArgumentException('Le message d\'erreur specifie a "'.$field.'" pour l\'erreur '.$error.' est invalide : '.$msg);
									}
								}
							}
							
							if($conf[0] == 'file')
							{
								if(!is_string($conf[3]))
								{
									throw new \InvalidArgumentException('Le nom spécifié pour le fichier "'.$field.'" est invalide');
								}
									
								if(!is_string($conf[4]))
								{
									throw new \InvalidArgumentException('Le chemin spécifié pour le fichier "'.$field.'" est invalide');
								}
									
								if(!is_dir($_SERVER["DOCUMENT_ROOT"].$conf[4]))
								{
									throw new \InvalidArgumentException('Le chemin "'.$conf[4].'" pour le fichier "'.$field.'" n\'existe pas sur le serveur');
								}
									
								if(!ctype_digit($conf[5]) && !is_int($conf[5]))
								{
									throw new \InvalidArgumentException('La taille maximum spécifié pour le fichier "'.$field.'" est invalide');
								}
									
								if(!is_array($conf[6]))
								{
									throw new \InvalidArgumentException('les extensions valides pour le fichier "'.$field.'" doit être sous forme de tableau');
								}
								else
								{
									foreach($conf[6] as $extension)
									{
										if(!is_string($extension))
										{
											throw new \InvalidArgumentException('le tableau d\'extensions pour le fichier "'.$field.'" contient une ou plusieurs données invalides');
										}
									}
								}
									
								$this->form_file = 'enctype="multipart/form-data"' ;
								$files_multiform[$field] = $conf[0] ;
							}
							
							if($conf[0] == 'textarea')
							{
								if(isset($conf[3]) && !is_numeric($conf[3]) && !empty($conf[3]))
								{
									throw new \InvalidArgumentException('La valeur rows specifie a '.$field.' est invalide : '.$conf[3]);
								}
								else
								{
									$conf[3] = 1 ;
								}
								
								if(isset($conf[4]) && !is_numeric($conf[4]) && !empty($conf[4]))
								{
									throw new \InvalidArgumentException('La valeur cols specifie a '.$field.' est invalide : '.$conf[4]);
								}
								else
								{
									$conf[4] = 1 ;
								}
								
								if(isset($conf[6]))
								{
									$arguments = $conf[6] ;
								}
							}
							else if($conf[0] != 'file' && isset($conf[4]))
							{
								$arguments = $conf[4] ;
							}
							
							/*if(isset($arguments) && !empty($arguments) && !is_array($arguments))
							{
								throw new \InvalidArgumentException('Le tableau d\'arguments specifie a '.$field.' est invalide');
							}
							elseif(isset($arguments))
							{
								foreach($arguments as $index => $value)
								{
									if(!in_array($index, $attributes) || !is_string($value))
									{
										throw new \InvalidArgumentException('Le tableau d\'arguments specifie a '.$field.' contient un ou plusieurs elements invalides');
									}
								}
							}*/
						}
						elseif($conf[0] == 'liste' || $conf[0] == 'multiple' || $conf[0] == 'radio')
						{
							if($conf[0] == 'radio' && isset($conf[6]) && is_object($conf[6]))
							{
								$this->adds_fields[] = $field ;	
							}
							elseif($conf[0] != 'multiple' && !in_array($field, $this->vars))
							{
								$debug = debug_backtrace();
								throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Le champ "'.$field.'" specifie dans $fields n\'est pas un attribut de l\'objet : '.$this->entity);
							}
							
							if(isset($conf[1]) && !is_string($conf[1]) && !empty($conf[1]))
							{
								throw new \InvalidArgumentException('Le Label specifie a '.$field.' est invalide : '.$conf[1]);
							}
							
							if(isset($conf[2]) && !is_string($conf[2]) && !empty($conf[2]))
							{
								throw new \InvalidArgumentException('Le message d\'erreur specifie a '.$field.' est invalide : '.$conf[2]);
							}
							
							if($conf[3] != 'many' && $conf[3] != 'one' && isset($conf[3]) && !empty($conf[3]))
							{
								throw new \InvalidArgumentException('Vous devez definir le type de relation pour '.$field.' a "many" ou "one" ou laisser vide si la liste n\'est pas une liste d\'objet : '.$conf[3]);
							}
							
							if(!is_array($conf[4]))
							{
								throw new \InvalidArgumentException('Les options de la liste specifie a '.$field.' doivent etre sous forme d\'un tableau');
							}
							elseif(count($conf[4]) > 0)
							{
								$keys_form = array_keys($conf[4]); 
								
								if(is_object($conf[4][$keys_form[0]]))
								{
									$RC = new \ReflectionClass($conf[4][$keys_form[0]]);
									$name = $RC->getName();
									
									foreach ($conf[4] as $entity) 
									{
										$RC_bis = new \ReflectionClass($entity);
										$name_bis = $RC_bis->getName();
										
										if($name != $name_bis)
										{
											throw new \InvalidArgumentException('Le Tableau d\'options de la liste specifie a '.$field.' ne contient pas des objets du meme type');
										}
									}
									
									$vars = $conf[4][$keys_form[0]]->getVars() ;
									$bol = false ;
									
									foreach ($vars as $nom => $valeur) 
									{
										if(isset($conf[5]) && $nom == $conf[5])
										{
											$bol = true ;	
										}
									}
									
									if($bol == false)
									{
										isset($conf[5]) ? $conf = $conf[5] : $conf = '' ; 
										throw new \InvalidArgumentException('L\'attribut de reference "'.$conf.'" specifie a '.$field.' n\'est pas un attribut de l\'objet : '.$name);
									}
								}
								else
								{
									foreach ($conf[4] as $attribut) 
									{
										if(!is_string($attribut))
										{
											throw new \InvalidArgumentException('Le Tableau d\'options de la liste specifie a '.$field.' doit contenir seulement des chaines de caracteres');
										}
									}
								}
								
								if(is_object($conf[4][$keys_form[0]]))
								{
									if(!isset($conf[5]) || !is_string($conf[5]) || empty($conf[5]))
									{
										throw new \InvalidArgumentException('L\'attribut de reference specifie a '.$field.' est invalide : '.$conf[5]);
									}
								}
								/*elseif(!isset($conf[4]) || !empty($conf[4]))
								{
									throw new \InvalidArgumentException('L\'attribut de reference specifie a '.$field.' doit etre vide si vous n\'utilisez pas des objets');
								}*/
							}
							
							if($conf[0] == 'multiple')
							{
								if(isset($conf[6]) && !is_int($conf[6]) && !empty($conf[6]))
								{
									throw new \InvalidArgumentException('Le nombre d\'elements minimum a selectionner specifie a '.$field.' est invalide : '.$conf[6]);
								}
							}
							
							if(isset($conf[7])) $arguments = $conf[7] ;
							
							if(isset($arguments) && !empty($arguments) && !is_array($arguments))
							{
								throw new \InvalidArgumentException('Le tableau d\'arguments specifie a '.$field.' est invalide');
							}
							elseif(isset($arguments))
							{
								foreach($arguments as $index => $value)
								{
									if(!in_array($index, $attributes) || !is_string($value))
									{
										throw new \InvalidArgumentException('Le tableau d\'arguments specifie a '.$field.' contient un ou plusieurs elements invalides');
									}
								}
							}
							
							$listes_multiform[$field] = $conf[0] ;
							
						}
						elseif($conf[0] == 'hidden')
						{
							if(in_array($field, $this->vars))
							{
								if(!isset($conf[1]) || (!empty($conf[1]) && !is_string($conf[1])))
								{
									throw new \InvalidArgumentException('La valeur specifie au champ hidden '.$field.' doit etre une chaine de caractere');
								}
							}
							else
							{
								throw new \InvalidArgumentException('Le champ "'.$field.'" specifie dans $fields n\'est pas un attribut de l\'objet : '.$this->entity);
							}
						}
						elseif($conf[0] != 'form')
						{
							if(isset($conf[1]) && !is_string($conf[1]) && !empty($conf[1]))
							{
								throw new \InvalidArgumentException('La valeur specifie a '.$field.' est invalide : '.$conf[1]);
							}
							
							if($conf[0] == 'submit')
							{
								$submit = $field ;
							}
						}
					}
					else
					{
						throw new \InvalidArgumentException('Le type de champ "'.$conf[0].'" est invalide');
					}
				}
				else
				{
					if($field === 'empty_form' && !is_string($conf))
					{
						throw new \InvalidArgumentException('Le message a afficher lorsque le formulaire est vide est invalide : '.$conf);
					}
					
					if($field === 'valide_form')
					{
						if(isset($conf) && !is_string($conf) && !empty($conf))
						{
							throw new \InvalidArgumentException('La valeur message de validation est invalide : '.$conf);
						}
						else
						{
							$this->valide = $conf ;	
						}
					}
				}
			}
			elseif($conf[0] == 'div' || $conf[0] == '/div' || $conf[0] == 'p' || $conf[0] == 'h')
			{
				if($conf[0] == 'div')
				{
					if(isset($conf[1]) && !is_string($conf[1]) && !empty($conf[1]))
					{
						throw new \InvalidArgumentException('La classe n\'est pas valide pour la balise '.$conf[1]);
					}
					
					if(isset($conf[2]) && !is_string($conf[2]) && !empty($conf[2]))
					{
						throw new \InvalidArgumentException('L\'id n\'est pas valide pour la balise '.$conf[2]);
					}
				}
				elseif($conf[0] == 'h')
				{
					if(!isset($conf[2]) || !is_string($conf[2]) || empty($conf[2]))
					{
						throw new \InvalidArgumentException('le contenu de la balise h est invalide');
					}
					
					if(!isset($conf[1]) || empty($conf[1]) || !ctype_digit($conf[1]) || $conf[1] < 1 || $conf[1] > 6)
					{
						throw new \InvalidArgumentException('la balise h doit aller de 1 a 6');
					}
					
					if(isset($conf[3]) && !is_string($conf[3]) && !empty($conf[3]))
					{
						throw new \InvalidArgumentException('La classe n\'est pas valide pour la balise h : '.$conf[3]);
					}
					
					if(isset($conf[4]) && !is_string($conf[4]) && !empty($conf[4]))
					{
						throw new \InvalidArgumentException('L\'id n\'est pas valide pour la balise h : '.$conf[4]);
					}
				}
				elseif($conf[0] == 'p')
				{
					if(!isset($conf[1]) || !is_string($conf[1]) || empty($conf[1]))
					{
						throw new \InvalidArgumentException('le contenu de la balise p est invalide');
					}
					
					if(isset($conf[2]) && !is_string($conf[2]) && !empty($conf[2]))
					{
						throw new \InvalidArgumentException('La classe n\'est pas valide pour la balise h : '.$conf[2]);
					}
					
					if(isset($conf[3]) && !is_string($conf[3]) && !empty($conf[3]))
					{
						throw new \InvalidArgumentException('L\'id n\'est pas valide pour la balise h : '.$conf[3]);
					}
				}
			}
			else
			{
				throw new \InvalidArgumentException('Le nom du champ "'.$field.'" est invalide');
			}
		}
		
		$this->fields = $fields ;
		$this->listes_multiform = $listes_multiform ;
		$this->files_multiform = $files_multiform ;
		
		$this->app = $app ;
		
		isset($submit) ? $submit = $submit : $submit = 'Enregistrer_'.$this->entity ;
			
		$id = $app->user()->key() ;
		
		if(!isset($_POST[$submit]))
		{
			$this->token = $app->user()->generateToken($this->entity.'_'.$id) ;
		}
		
		if($class != '')
		{
			if(!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9-_]*[ ]?)*$/',$class))
			{
				throw new \InvalidArgumentException('Le nom de classe ($class) n\'est pas valide : '.$class);	
			}
			
			$this->class = $class ;
		}
		else
		{
			$this->class = $this->entity ;
		}
		
		if($class_bouton != '')
		{
			if(!preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9-_]*[ ]?)*$/',$class_bouton))
			{
				throw new \InvalidArgumentException('Le nom de classe pour la div des boutons ($class_bouton) n\'est pas valide : '.$class);	
			}
			
			$this->class_bouton = $class_bouton ;
		}
		else
		{
			$this->class_bouton = 'bouton_'.$this->entity ;
		}
		
		if($class_form != '')
		{
			if(!preg_match('/^([a-zA-Z_]{1}[a-zA-Z0-9-_]*[ ]?)*$/',$class_form))
			{
				throw new \InvalidArgumentException('Le nom de classe ($class_form) n\'est pas valide : '.$class_form);
			}
			
			$this->class_form = $class_form ;
		}
		else
		{
			$this->class_form = 'formulaire_'.$this->entity ;
		}
		
		$this->id_form = 'formulaire_'.$this->entity.'_'.$this->id ;
	}
	
	public function form ($active = true)
	{
		$script = '' ;
		$append = 'nom_form' ;
		$display_error = '' ;
		$nb_erreur = 10000 ;
		$msg = '' ;
		$add = '<input type="button" id="new_multiform_element" value="Ajouter '.$this->nom.'" name="New_'.$this->entity.'_'.$this->id.'" />';
		$token = '<input type="hidden" name="token" value="'.$this->token.'"/>' ;
		
		foreach($this->fields as $field => $conf)
		{
			if($field != 'valide_form')
			{
				$field_champ = '' ;
				$count = 0 ;
				$errors = array() ;
				
				if($field === 'empty_form')
				{
					$msg = '<p class="empty_'.$this->entity.'">'.$conf.'</p></br>' ;
				}
				elseif($conf[0] == 'form')
				{
					$form= '<form action="" method="post" name="'.$field.'" '.$this->form_file.'>' ;	
				}
				elseif($conf[0] == 'submit')
				{
					isset($conf[1]) ? $submit = $conf[1] : $submit = 'enregistrer' ;
					
					$submit = '<input type="submit" value="'.$submit.'" name="'.$field.'" />' ;	
				}
				elseif($conf[0] == 'div' || $conf[0] == '/div' || $conf[0] == 'p' || $conf[0] == 'h')
				{
					$replace_field = str_replace(' ','',$field);
					
					if($conf[0] === 'div')
					{
						$balise = '<div' ;
						
						if(isset($conf[1]))
						{
							$balise .= ' class="'.$conf[1].'"' ;
						}
						
						if(isset($conf[2]))
						{
							$balise .= ' id="'.$conf[2].'"' ;
						}
						
						$balise .= '>' ;
					}
					elseif($conf[0] === '/div')
					{
						$balise = '</div>' ;
					}
					elseif($conf[0] === 'h')
					{
						$balise = '<'.$conf[0].$conf[1] ;
						
						if(isset($conf[3]))
						{
							$balise .= ' class="'.$conf[3].'"' ;
						}
						
						if(isset($conf[4]))
						{
							$balise .= ' id="'.$conf[4].'"' ;
						}
						
						$balise .= '>'.$conf[2].'</h'.$conf[1].'>' ;
					}
					elseif($conf[0] === 'p')
					{
						$balise = '<'.$conf[0] ;
						
						if(isset($conf[2]))
						{
							$balise .= ' class="'.$conf[2].'"' ;
						}
						
						if(isset($conf[3]))
						{
							$balise .= ' id="'.$conf[3].'"' ;
						}
						
						$balise .= '>'.$conf[1].'</p>' ;
					}
					
					$append .= '+'.$replace_field ;
					$script .= 'var '.$replace_field.'= \''.$balise.'\' ;' ;
				}
				elseif($conf[0] != 'hidden')
				{
					if($conf[1] != '')
					{
						$field_label = 'label_'.$field ;
						$script .= 'var '.$field_label.'= "<label>'.$conf[1].'</label>" ;' ;
						$append .= '+'.$field_label ;
					}
					
					$append .= '+'.$field ;
					
					if($conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form' && $conf[0] != 'multiple' && $conf[0] != 'liste' && $conf[0] != 'radio')
					{
						if($conf[0] == 'textarea' && isset($conf[6]))
						{
							$script .= 'var '.$field.'= \'<textarea name="new_'.$field.'_\'+count_form_'.$this->id.'+\'"' ;
							$arguments = $conf[6] ;
							$end = '</textarea>\' ;' ;
							
							if(isset($arguments) && is_array($arguments))
							{
								//die(var_dump($arguments));
								foreach($arguments as $index => $value)
								{
									$value = str_replace("'", "\'", $value) ;
									$script .= ' '.$index.' ="'.$value.'"';
								}
							}
							
							$script .= '>' ;
							
						}
						elseif($conf[0] != 'textarea' && isset($conf[4]))
						{
							$script .= 'var '.$field.'= \'<input type="'.$conf[0].'" name="new_'.$field.'_\'+count_form_'.$this->id.'+\'" value=""' ;
							$arguments = $conf[4] ;
							$end = '/>\' ;' ;
							
							if(isset($arguments) && is_array($arguments))
							{
								foreach($arguments as $index => $value)
								{
									$script .= ' '.$index.' ="'.$value.'"' ;
								}
							}
						}
						
						$script .= $end ;
					}
					elseif($conf[0] == 'liste' || $conf[0] == 'multiple')
					{
						$conf[0] == 'multiple' ? $multiple = 'multiple="multiple"' : $multiple = '' ;
						
						$field_champ = '<select name="new_'.$field.'_\'+count_form_'.$this->id.'+\'[]" '.$multiple ;
						
						if(isset($conf[7]))
						{
							foreach($conf[7] as $index => $value)
							{
								$field_champ .= ' '.$index.' ="'.$value.'"' ;
							}
						}
						
						$field_champ .= '>' ;
						
						if($conf[0] == 'liste' && isset($conf[6]) && $conf[6] == 0)
						{
							$field_champ .= '<option value=""> </option>' ;
						}
						
						foreach($conf[4] as $index => $entity)
						{	
							if(is_object($entity))
							{
								$field_champ .= '<option value="'.$this->crypt->encrypt($entity->id()).'">'.$entity->$conf[5]().'</option>' ;
							}
							else
							{
								$field_champ .= '<option value="'.$index.'">'.$entity.'</option>' ;
							}
						}
						
						$field_champ .= '</select>' ;
						
						$script .= 'var '.$field.'= \''.$field_champ.'\' ;' ;
					}
					elseif($conf[0] == 'radio')
					{
						$checked = false ;
						
						foreach($conf[4] as $index => $entity)
						{
							$checked === false ? $checked = 'checked' : $checked = '' ;
							
							is_object($entity) ? $champ = '<input type="radio" name="new_'.$field.'_\'+count_form_'.$this->id.'+\'" value="'.$this->crypt->encrypt($entity->id()).'" '.$checked.'>'.$entity->$conf[5]() : $champ = '<input type="radio" name="new_'.$field.'_\'+count_form_'.$this->id.'+\'" value="'.$index.'" '.$checked.'>'.$entity ;
							
							$field_champ .= $champ ;
						}
						
						$script .= 'var '.$field.'= \''.$field_champ.'\' ;' ;
					}
				}
				else
				{
					$append .= '+'.$field ;
					$script .= 'var '.$field.'= \'<input type="hidden" name="new_'.$field.'_\'+count_form_'.$this->id.'+\'" value="'.$conf[1].'" />\' ;' ;
				}
				
			}
		}
		
		$js = ' if (typeof remove_item != "function") 
				{  
					function remove_item ($item)
					{
						$($item).remove(); 
					}
				}
									
				if (typeof init_item != "function") 
				{  
					function init_item (){}
				}
				
				$(document).ready(Ajouter_'.$this->entity.'_'.$this->id.');
				function Ajouter_'.$this->entity.'_'.$this->id.'()
				{
					$("input[name=\'New_'.$this->entity.'_'.$this->id.'\']").click(function()
					{
						$(\'#'.$this->id_form.'\').append(\'<div class="'.$this->class.' new_item_form" id="'.$this->entity.'_'.$this->id.'_\'+count_form_'.$this->id.'+\'"></div>\');
						
						var nom_form = \'<p class="name_'.$this->entity.'">'.$this->nom.' \'+count_form_'.$this->id.'+\'</p>\' ;
						
						'.$script.'
						
						var supprimer_form = \'<input type="button" value="Supprimer" name="Supr_entity" id="\'+count_form_'.$this->id.'+\'" onclick="remove_item(\\\'#'.$this->entity.'_'.$this->id.'_\\\'+this.id);" />\' ;
						var id_form = \'<input type="hidden" name="new_idform_\'+count_form_'.$this->id.'+\'" value="\'+count_form_'.$this->id.'+\'"/>\' ;
						
						$(\'#'.$this->entity.'_'.$this->id.'_\'+count_form_'.$this->id.' ).append('.$append.'+id_form+supprimer_form);
						
						count_form_'.$this->id.' ++ ;
								
						init_item() ;
					});
					
					if(typeof new_erreurs_'.$this->entity.' != "undefined")
					{
						for(var key in new_erreurs_'.$this->entity.')
						{
							'.$display_error.'
						}
					}
				}
		';
			
		$search = array("\t", "\n", "\r");
 	 	$js = str_replace($search,'',$js);
		
		if(!isset($form)) $form = '<form action="" method="post" name="'.$this->entity.'" '.$this->form_file.'>' ;
		if(!isset($submit)) $submit = '<input type="submit" value="Enregistrer" name="Enregistrer_'.$this->entity.'" />' ;
		
		if(count($this->entities) == 0)
		{
			$div = '<div class="'.$this->class_form.'" id="'.$this->id_form.'"></div>' ;
			
			$script = '<script>var count_form_'.$this->id.' = '.$this->count.'; '.$js.'</script>' ;
			
			if($active === true) 
			{
				return $script.$msg.$form.$div.'<div class="'.$this->class_bouton.'">'.$add.$submit.'</div>'.$token.'</form>' ;
			}
			elseif($active === false)
			{
				return $script.$msg.$div.$add ;
			}
		}
		else
		{
			$div = '<div class="'.$this->class_form.'" id="'.$this->id_form.'">' ;
			$new_erreurs = 'new_erreurs_'.$this->entity;
			
			if($active === true) 
			{
				$formulaire = $form.$div ;
			}
			elseif($active === false)
			{
				$formulaire = $div ;
			}
			
			foreach($this->entities as $entity)
            {
				$erreur = 'erreur_'.$entity['id'] ;
				$block_entity = '<div class="'.$this->class.'" id="'.$this->entity.'_'.$this->id.'_'.$this->count.'">' ;
				$titre = '<p class="name_'.$this->entity.'">'.$this->nom.' '.$this->count.'</p>' ;
				$field_erreur = '' ;
				$encrypt_id = $this->crypt->encrypt($entity['id']) ;
				
				$formulaire .= $block_entity.$titre ;
				
				if(!preg_match('/^new_/',$entity['id']))
				{
					$name_field = '';
					$name_form = 'erreurs';
				}
				else
				{
					$name_field = 'new_';
					$name_form = 'new_erreurs';
				}
				
				foreach($this->fields as $field => $conf)
				{
					$errors = array() ;
					$field_champ = '' ;
					
					if($field != 'empty_form' && $field != 'valide_form' && $conf[0] != 'div' && $conf[0] != '/div' && $conf[0] && $conf[0] != 'h' && $conf[0] != 'p')
					{
						if($conf[0] != 'hidden' && $conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form' && $conf[0] != 'multiple' && $conf[0] != 'liste' && $conf[0] != 'add' && $conf[0] != 'radio')
						{
							$field_label = '' ;
							
							if(($conf[0] != 'textarea' && (!isset($conf[3]) || !is_object($conf[3]))) || ($conf[0] == 'textarea' && (!isset($conf[5]) || !is_object($conf[5]))))
							{
								foreach($conf[2] as $error => $msg)
								{
									if($error != 'verification' || $conf[0] != 'password')
									{
										$const = strtoupper($field.'_'.$error) ;
										
										if(defined($this->namespace."::$const"))
										{
											$value = constant($this->namespace."::$const") ;
										}
										else
										{
											throw new \InvalidArgumentException('La constante '.$const.' n\'existe pas pour l\'objet : '.$this->entity);
										}
										
										$msg != '' ? $msg_error = $msg : $msg_error = $field.' '.$error ;
										
										if(isset($this->{$name_form}[$entity['id']]))
										{
											if (count($this->$name_form) > 0 && in_array($value, $this->{$name_form}[$entity['id']])) 
											{
												$field_erreur = '<p class="msg_erreur">'.$msg_error.'</p>';
												$formulaire .= $field_erreur ;
											}
										}
										
										$errors[] = strtoupper($error) ;
									}
								}
								
								$const = strtoupper($field.'_INVALIDE') ;
								$value = constant($this->namespace."::$const");
								
								if(!in_array('INVALIDE', $errors) && count($this->$name_form) > 0 && in_array($value, $this->{$name_form}[$entity['id']]))
								{
									$msg_error = 'Invalide' ;
									$field_erreur = '<p class="msg_erreur">'.$msg_error.'</p>';
									$formulaire .= $field_erreur ;
								}
								
								$const = strtoupper($field.'_INDISPONIBLE') ;
								
								if(defined($this->namespace."::$const"))
								{
									$value = constant($this->namespace."::$const");
									
									if(!in_array('INDISPONIBLE', $errors) && count($this->$name_form) > 0 && in_array($value, $this->{$name_form}[$entity['id']]))
									{
										$msg_error = 'Déjà utilisé' ;
										$field_erreur = '<p class="msg_erreur">'.$msg_error.'</p>';
										$formulaire .= $field_erreur;
									}
								}
							}
							
							if($conf[1] != '')
							{
								$field_label = '<label>'.$conf[1].'</label>' ;	
							}
							
							if($conf[0] == 'password')
							{
								$field_champ = '<input type="password" name="'.$name_field.$field.'_'.$encrypt_id.'" value="" />' ; 
							}
							else
							{
								if(($conf[0] != 'textarea' && $conf[0] != 'file' && isset($conf[3]) && !empty($conf[3])) || ($conf[0] == 'textarea' && isset($conf[5]) && !empty($conf[5])))
								{
									$conf[0] != 'textarea' ? $confnum = 3 : $confnum = 5;
									
									$RC = new \ReflectionClass($conf[3]);
									$namespace = $RC->getName();
								
									$target = explode("\\", $namespace);
									$target = ucfirst($target[count($target)-1]);
										
									if($name_field === '' && !isset($_POST[$name_field.$field.'_'.$encrypt_id]))
									{
										$value = $this->managers->getManagerOf($target)->DEF->getArgAssociate($field, $entity['id'], $this->entity , $conf[$confnum]->id()) ;
									}
									else
									{
										if($name_field == '')
										{
											isset($_POST[$name_field.$field.'_'.$encrypt_id]) ? $value = $_POST[$name_field.$field.'_'.$encrypt_id] : $value = '' ;
										}
										else
										{
											isset($this->adds[$this->crypt->decrypt($encrypt_id)][$target][$field]) ? $value = $this->adds[$this->crypt->decrypt($encrypt_id)][$target][$field] : $value = '' ;
										}
									}
									
									foreach($conf[2] as $error => $msg)
									{
							        	$nb_erreur ++ ;
										
										if(count($this->$name_form) > 0 && isset($this->{$name_form}[$entity['id']]) && in_array($nb_erreur, $this->{$name_form}[$entity['id']]))
										{
											$field_erreur = '<p class="msg_erreur">'.$msg.'</p>';
											$formulaire .= $field_erreur ;
										}
									}
								}
								else
								{
									$value = $entity[$field] ;
								}
								
								if($conf[0] != 'textarea' && $conf[0] != 'file') 
								{
									$field_champ = '<input type="'.$conf[0].'" name="'.$name_field.$field.'_'.$encrypt_id.'" value="'.$value.'"' ;
									
									if(isset($conf[4]))
									{
										foreach($conf[4] as $index => $value)
										{
											$field_champ .= ' '.$index.' ="'.$value.'"' ;
										}
									}
									
									$field_champ .= '/>' ;
								}
								elseif($conf[0] == 'file')
								{
									if(!is_string($value)) $value = '' ;
									
									for($a = 1 ; $a < 4 ; $a++)
									{
										$nb_erreur ++ ;
									
										if($a == 1)
										{
											$field_erreur = '<p class="msg_erreur">Le fichier est trop volumineux</p>' ;
										}
										elseif($a == 2)
										{
											$field_erreur = '<p class="msg_erreur">Le fichier n\'est pas valide</p>' ;
										}
										elseif($a == 3)
										{
											$field_erreur = '<p class="msg_erreur">Erreur lors de l\'upload du fichier</p>' ;
										}

										if(isset($entity['id']) && isset($tab_erreur[$entity['id']]) && count($tab_erreur[$entity['id']]) > 0 && in_array($nb_erreur, $tab_erreur[$entity['id']]))
										{
											$formulaire .= $field_erreur ;
										}
									}
									
									$field_champ = '<input type="file" name="'.$name_field.$field.'_'.$encrypt_id.'">' ;
									
									$path = $conf[4].$value;
										
									if(!empty($value) && file_exists ($_SERVER["DOCUMENT_ROOT"].$path))
									{
										$field_champ .= '<p>fichier actuel :<p>' ;
											
										if(exif_imagetype($_SERVER["DOCUMENT_ROOT"].$path) && isset($conf[7]) && $conf[7] == 1)
										{
											$field_champ .= '<img src="'.$path.'" alt="'.$value.'">' ;
										}
										else
										{
											$field_champ .= '<p>'.$value.'</p>' ;
										}
									}
								}
								else
								{
									$field_champ = '<textarea name="'.$name_field.$field.'_'.$encrypt_id.'" rows ="'.$conf[3].'" cols ="'.$conf[4].'"' ; 
									
									if(isset($conf[6]))
									{
										foreach($conf[6] as $index => $valeur)
										{
											$field_champ .= ' '.$index.' ="'.$valeur.'"' ;
										}
									}
									
									$field_champ .= '>'.$value.'</textarea>' ;
								}
							} 
							
							$formulaire .= $field_label.$field_champ;
							
							if($conf[0] == 'password')
							{
								if(isset($conf[3]) && !is_bool($conf[3]) && !empty($conf[3]))
								{
									throw new \InvalidArgumentException('La valeur de verification pour le champ password specifie a '.$field.' doit etre de type bool : '.$conf[3]);
								}
								elseif($conf[3] === true)
								{
									$const = strtoupper($field.'_VERIFICATION') ;
									
									if(defined($this->namespace."::$const"))
									{
										$value = constant($this->namespace."::$const") ;
									}
									else
									{
										throw new \InvalidArgumentException('La constante '.$const.' n\'existe pas pour l\'objet : '.$this->entity);
									}
									
									$field_champ = '<input type="password" name="'.$name_field.$field.'_'.$encrypt_id.'_verification" value="" /><br />' ; 
									
									isset($conf[2]['verification']) ? $msg_error = $conf[2]['verification'] : $msg_error = 'La vérification n\'est pas bonne' ;
									
									$field_erreur = '<p class="msg_erreur">'.$msg_error.'</p>';
									
									if (count($this->$name_form) > 0 && in_array($value, $this->$name_form)) 
									{
										$formulaire .= $field_erreur ;
									}
									
									$formulaire .= $field_label.$field_champ;
								}
							}
						}
						elseif($conf[0] == 'liste' || $conf[0] == 'multiple' || $conf[0] == 'radio')
						{
							$tab_liste = array() ;
							$nb_erreur ++ ;
							
							isset($conf[1]) ? $field_label = '<label>'.$conf[1].'</label>' : $field_label = '' ;
							isset($conf[2]) ? $field_erreur = '<p class="msg_erreur">'.$conf[2].'</p>' : $field_erreur = '<p class="msg_erreur">'.$field.' Invalide</p>' ;
							
							
							if($conf[0] == 'liste' || $conf[0] == 'multiple')
							{
								$conf[0] == 'multiple' ? $multiple = 'multiple="multiple"' : $multiple = '' ;
								$field_champ = '<select name="'.$name_field.$field.'_'.$encrypt_id.'[]" '.$multiple.'>' ;
							}
							
							if($conf[0] == 'liste' && isset($conf[6]) && $conf[6] == 0)
							{
								$field_champ .= '<option value=""> </option>' ;
							}
							
							$count = count($conf[4]) ;
							
							if($count > 0)
							{
								$keys_form = array_keys($conf[4]); 
								
								if(is_object($conf[4][$keys_form[0]]))
								{
									$RC = new \ReflectionClass($conf[4][$keys_form[0]]);
									$name = $RC->getName();
									
									$exp = explode("\\", $name);
									$name = $exp[count($exp)-1];
									
									if($conf[3] === 'many')
									{
										if(!$entity->isNew())
										{
											$name = strtolower($name) ;
											$liste = $this->managers->getManagerOf($this->entity)->DEF->getAssociate($entity['id'], $name) ;
											
											foreach($liste as $entity_liste)
											{
												$tab_liste[] = $entity_liste[$name] ;
											}
										}
										elseif(isset($this->multi[$entity['id']][$name]))
										{
											$tab_liste = $this->multi[$entity['id']][$name] ;
										}
									}
									elseif($conf[3] === 'one')
									{
										if(!$entity->isNew())
										{
											$arg = $this->managers->getManagerOf($this->entity)->DEF->get($field, $entity['id']) ;
											$tab_liste[] = $this->crypt->encrypt((string)$arg) ;
										}
										else
										{
											$tab_liste[] = $this->crypt->decrypt($entity[$field][0]) ;
										}
									}
								}
								else
								{
									if($conf[0] === 'radio' && isset($conf[6]))
									{
										$RC = new \ReflectionClass($conf[6]);
										$target = $RC->getName();
										
										$exp = explode("\\", $target);
										$target = $exp[count($exp)-1];
									
										//!isset($_POST[$name_field.$field.'_'.$encrypt_id]) ? $tab_liste[] = $this->managers->getManagerOf($target)->DEF->getArgAssociate($field, $entity['id'], $this->class , $conf[6]->id()) : $tab_liste[] = $_POST[$name_field.$field.'_'.$encrypt_id] ;
										
										if(!isset($_POST[$name_field.$field.'_'.$encrypt_id]) && !$entity->isNew() && isset($entity->$field))
										{
											$tab_liste[] = $entity->$field ;
										}
										elseif(!$entity->isNew())
										{
											$tab_liste[] = $this->managers->getManagerOf($target)->DEF->getArgAssociate($field, $entity['id'], $this->entity , $conf[6]->id()) ;
										}
										elseif(isset($_POST[$name_field.$field.'_'.$encrypt_id]))
										{
											$tab_liste[] = $_POST[$name_field.$field.'_'.$encrypt_id] ;
										}
									}
                                    else
                                    {
                                        if(!isset($_POST[$name_field.$field.'_'.$encrypt_id]) && !$entity->isNew() && isset($entity->$field))
										{
											$tab_liste[] = $entity->$field ;
										}
										elseif(!$entity->isNew())
										{
                                            $arg = $this->managers->getManagerOf($this->entity)->DEF->get($field, $entity['id']) ;
                                            $tab_liste[] = $arg ;
										}
                                    }
								}
							}
							
							$i = 0 ;
						
							//var_dump($tab_liste) ;
							
							foreach($conf[4] as $index => $entity_form)
							{
								if(is_object($entity_form))
								{
									if($conf[0] == 'liste' || $conf[0] == 'multiple')
									{
										in_array($entity_form->id(), $tab_liste) ? $selected = 'selected' : $selected = '' ;
										$field_champ .= '<option value="'.$this->crypt->encrypt((string)$entity_form->id()).'" '.$selected.'>'.$entity_form->$conf[5]().'</option>' ;
									}
									elseif($conf[0] == 'radio')
									{
										if(in_array($entity_form->id(), $tab_liste) || $i == 0)
										{
											$selected = 'checked' ;
										}
										else
										{
											$selected = '' ;
										}
										
										$field_champ .= '<input type="radio" name="'.$name_field.$field.'" value="'.$this->crypt->encrypt($entity_form->id()).'" '.$selected.'>'.$entity_form->$conf[5]() ;
									}
								}
								else
								{
									if($conf[0] == 'liste' || $conf[0] == 'multiple')
									{
										in_array($entity_form, $tab_liste) ? $selected = 'selected' : $selected = '' ;
										$field_champ .= '<option value="'.$entity_form.'" '.$selected.'>'.$entity_form.'</option>' ;
									}
									elseif($conf[0] == 'radio')
									{
										if(in_array($index, $tab_liste) || $i === 0) 
										{
											$selected = 'checked="checked"' ;
										}
										else
										{
											$selected = '' ;
										}
										
										$field_champ .= '<input type="radio" name="'.$name_field.$field.'_'.$encrypt_id.'" value="'.$index.'"'.$selected.'>'.$entity_form ;
									}
								}
								
								$i ++ ;
							}
							
							if($conf[0] == 'liste' || $conf[0] == 'multiple')
							{
								$field_champ .= '</select>' ;
							}
							
							if (count($this->$name_form) > 0 && in_array($nb_erreur, $this->{$name_form}[$entity['id']])) 
							{
								$formulaire .= $field_erreur ;
							}
							
							$formulaire .= $field_label.$field_champ;
							
						}
						elseif($conf[0] == 'hidden' && in_array($field, $this->vars))
						{
							if(isset($entity[$field]))
							{
								empty($conf[1]) ? $value = $entity[$field] : $value = $conf[1] ;
								
								if($field == 'id')
								{
									$value = $this->crypt->encrypt((string)$value) ;
								}
								
								$hidden = '<input type="hidden" name="'.$name_field.$field.'_'.$encrypt_id.'" value="'.$value.'"/>' ;	
								$formulaire .= $hidden ;
							}
							else
							{
								throw new \InvalidArgumentException('Le champ hidden '.$field.' n\'est pas un attribut ou n\'est pas attribue a l\'objet : '.$this->entity);
							}
						}
						elseif($conf[0] != 'form' && $conf[0] != 'submit')
						{
							$champ = '<input type="'.$conf[0].'" name="'.$name_field.$field.'_'.$encrypt_id.'" value="'.$conf[1].'"/>' ;
							$formulaire .= $champ ;
						}
					}
					elseif($conf[0] === 'div' || $conf[0] === '/div' || $conf[0] === 'p' || $conf[0] === 'h')
					{
						if($conf[0] === 'div')
						{
							$balise = '<'.$conf[0] ;
							
							if(isset($conf[1]))
							{
								$balise .= ' class="'.$conf[1].'"' ;
							}
							
							if(isset($conf[2]))
							{
								$balise .= ' id="'.$conf[2].'"' ;
							}
							
							$balise .= '>' ;
							
							$formulaire .= $balise ;
						}
						elseif($conf[0] === '/div')
						{
							$formulaire .= '</div>' ;
						}
						elseif($conf[0] === 'h')
						{
							$balise = '<'.$conf[0].$conf[1] ;
							
							if(isset($conf[3]))
							{
								$balise .= ' class="'.$conf[3].'"' ;
							}
							
							if(isset($conf[4]))
							{
								$balise .= ' id="'.$conf[4].'"' ;
							}
							
							$balise .= '>'.$conf[2].'</h'.$conf[1].'>' ;
							
							$formulaire .= $balise ;
						}
						elseif($conf[0] === 'p')
						{
							$balise = '<'.$conf[0] ;
							
							if(isset($conf[2]))
							{
								$balise .= ' class="'.$conf[2].'"' ;
							}
							
							if(isset($conf[3]))
							{
								$balise .= ' id="'.$conf[3].'"' ;
							}
							
							$balise .= '>'.$conf[1].'</p>' ;
							
							$formulaire .= $balise ;
						}
					}
				}
				
				$supr = '<input type="button" value="Supprimer" name="Supr_entity" id="'.$this->count.'" onclick="$(\'#'.$this->entity.'_'.$this->id.'_\'+this.id).remove();"/>' ;
				$formulaire .= '<input type="hidden" name="'.$name_field.'idform_'.$encrypt_id.'" value="'.$encrypt_id.'"/>'.$supr.'</div>' ;
				$this->count ++ ;
			}
			
			$script = "<script>var count_form_".$this->id." = ".$this->count." ;";
			if(isset($$new_erreurs)){ $$new_erreurs = json_encode($$new_erreurs) ; $script .= "var $new_erreurs = $$new_erreurs ;" ;}
			$script .= $js."</script>" ;
			
			if($active === true) 
			{
				$formulaire .= $script.'</div><div class="'.$this->class_bouton.'">'.$add.$submit.'</div>'.$token.'</form>';
			}	
			elseif($active === false) 
			{
				$formulaire .= $script.'</div>'.$add ;
			}
			
			return $formulaire ;
		}
	}
	
	public function processMultiform($validate = false, array $add = array(), $token = true)
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
		
		$id = $this->app->user()->key() ;
		
		if(!$this->app->user()->isValidToken(1200, $url, $this->entity.'_'.$id) && $token === true)
		{
			$this->app->user()->setFlash('Formulaire expiré');
			$this->app->httpResponse()->redirect($_SERVER['REQUEST_URI']);
		}
		
		if(!is_bool($validate) && $validate != 1)
		{
			throw new \InvalidArgumentException('La valeur de verifiaction de validation $validate doit etre ude type bool ou egal a 1');	
		}
		else
		{
			$multiform_validate_entities = array() ;	
		}
		
		if($validate === false || $validate === 1)
		{
			$token_multiform = $this->app->user()->generateToken($this->entity.'_'.$id) ;
		}
		else
		{
			$token_multiform = $this->token ;
		}
		
		if(count($add) > 0 )
		{
			foreach($add as $attribut => $val)
			{
				if(!in_array($attribut, $this->vars))
				{
					$debug = debug_backtrace();
					throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : L\'attribut '.$attribut.' n\'est pas valide pour l\'objet "'.$this->entity.'"');	
				}
				
				if(empty($val))
				{
					$debug = debug_backtrace();
					throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : La valeur de l\'attribut '.$attribut.' ne peut pas etre vide');
				}
			}
		}
		
		$new_erreur_multiform = 'new_erreur_'.$this->entity;
		$erreurs_multiform = 'erreurs_'.$this->entity;
		$class_multiform = $this->namespace ;
		$nb_erreur = 10000 ;
		
		$keys_multiform = array() ;
		$new_keys_multiform = array() ;
		$old_entity_multiform = array() ;
		$champs_multiform = array() ;
		$new_champs_multiform = array() ;
		$array_entity_multiform = array() ;
		$array_new_entity_multiform = array() ;
		$erreur_multiform = 0 ;
		$$new_erreur_multiform = array() ;
		$$erreurs_multiform = array() ;
		$multi_multiform = array() ;
		$liste_erreur_multiform = array() ;
		$adds_erreur_multiform = array() ;
		$adds_multiform = array() ;
		
		$modify_multiform = array() ;
		$new_multiform = array() ;
		
		$request_form = array_merge($_POST, $_FILES);
		
		foreach($this->fields as $field_multiform => $conf_multiform)
		{
			if($conf_multiform[0] != 'div' && $conf_multiform[0] != '/div' && $conf_multiform[0] != 'p' && $conf_multiform[0] != 'h')
			{
				foreach($request_form as $key_multiform => $val_multiform)
				{
					if(preg_match("/^new_idform_/", $key_multiform) || preg_match("/^idform_/", $key_multiform))
					{
						$replace_multiform = $val_multiform ;
						
						if(preg_match("/^new_idform_/", $key_multiform))
						{
							if(!in_array($replace_multiform, $new_keys_multiform)) $new_keys_multiform[] = $replace_multiform;
						}
						elseif(preg_match("/^idform_/", $key_multiform))
						{
							if(!in_array($this->crypt->decrypt($replace_multiform), $keys_multiform)) $keys_multiform[] = $this->crypt->decrypt($replace_multiform) ;
						}
					}
					
					if(in_array($field_multiform, $this->vars))
					{
						if(preg_match("/^".$field_multiform."_/", $key_multiform))
						{
							$supr_multiform = array($field_multiform."_" => "");
							$replace_multiform = strtr($key_multiform, $supr_multiform);
							$replace_multiform = intval($this->crypt->decrypt($replace_multiform));
							$tab_multiform = 'tab_'.$field_multiform ;
							${$tab_multiform}[$replace_multiform] = $val_multiform ;
							$champs_multiform[$tab_multiform] = $field_multiform ;
							if(!in_array($replace_multiform, $keys_multiform)) $keys_multiform[] = $replace_multiform ;
						}
						elseif(preg_match("/^new_".$field_multiform."_/", $key_multiform))
						{
							$supr_multiform = array("new_".$field_multiform."_" => "");
							$replace_multiform = strtr($key_multiform, $supr_multiform);
							$var_multiform = 'new_tab'.$field_multiform ;
							${$var_multiform}[$replace_multiform] = $val_multiform ;
							$new_champs_multiform[$var_multiform] = $field_multiform ;
							if(!in_array($replace_multiform, $new_keys_multiform)) $new_keys_multiform[] = $replace_multiform;
						}
					}
				}
			}
		}
	
		if(count($this->listes_multiform) > 0)
		{
			foreach($keys_multiform as $key_multiform) 
			{
                $key_multiform = $this->crypt->encrypt($key_multiform) ;
				foreach($this->listes_multiform as $field_form => $type_multiform)
				{
					$nb_erreur ++ ;
					
					if($type_multiform == 'liste')
					{
						if((!isset($this->fields[$field_form][6]) || $this->fields[$field_form][6] != 0) && (!isset($_POST[$field_form.'_'.$key_multiform]) || empty($_POST[$field_form.'_'.$key_multiform])))
						{
							$liste_erreur_multiform[$key_multiform][] = $nb_erreur ; 
						}
					}
					elseif($type_multiform == 'multiple')
					{
						if((!isset($this->fields[$field_form][6]) || $this->fields[$field_form][6] != 0) && (!isset($_POST[$field_form.'_'.$key_multiform]) || empty($_POST[$field_form.'_'.$key_multiform]) || count($_POST[$field_form.'_'.$key_multiform]) == 0 || (isset($this->fields[$field_form][6]) && count($_POST[$field_form.'_'.$key_multiform]) < $this->fields[$field_form][6])))
						{
							$liste_erreur_multiform[$key_multiform][] = $nb_erreur ; 
						}
						else
						{
							$RC = new \ReflectionClass($this->fields[$field_form][4][0]);
							$name = $RC->getName();
							
							$entity_associate = explode("\\", $name);
							$entity_associate = $entity_associate[count($entity_associate)-1];
							
							$multi_multiform[$key_multiform][$entity_associate] = array() ;
							
							foreach($_POST[$field_form.'_'.$key_multiform] as $key_multi)
							{
								$multi_multiform[$key_multiform][$entity_associate][] = $this->crypt->decrypt($key_multi) ;
							}
						}
					}
				}
			}
			
			foreach($new_keys_multiform as $new_key_multiform) 
			{
				foreach($this->listes_multiform as $field_form => $type_multiform)
				{
					$nb_erreur ++ ;
					
					if($type_multiform == 'liste')
					{
						if((!isset($this->fields[$field_form][6]) || $this->fields[$field_form][6] != 0) && (!isset($_POST['new_'.$field_form.'_'.$new_key_multiform]) || empty($_POST['new_'.$field_form.'_'.$new_key_multiform])))
						{
							$liste_erreur_multiform[$new_key_multiform][] = $nb_erreur ; 
						}
					}
					elseif($type_multiform == 'multiple')
					{
						if((!isset($this->fields[$field_form][6]) || $this->fields[$field_form][6] != 0) && (!isset($_POST['new_'.$field_form.'_'.$new_key_multiform]) || empty($_POST['new_'.$field_form.'_'.$new_key_multiform]) || count($_POST['new_'.$field_form.'_'.$new_key_multiform]) == 0 || (isset($this->fields[$field_form][6]) && count($_POST['new_'.$field_form.'_'.$new_key_multiform]) < $this->fields[$field_form][6])))
						{
							$liste_erreur_multiform[$new_key_multiform][] = $nb_erreur ; 
						}
						else
						{
							$RC = new \ReflectionClass($this->fields[$field_form][4][0]);
							$name = $RC->getName();
							
							$entity_associate = explode("\\", $name);
							$entity_associate = $entity_associate[count($entity_associate)-1];
							
							$multi_multiform[$new_key_multiform][$entity_associate] = array() ;
							
							foreach($_POST['new_'.$field_form.'_'.$new_key_multiform] as $key_multiform)
							{
								$multi_multiform[$new_key_multiform][$entity_associate][] = $this->crypt->decrypt($key_multiform) ;
							}
						}
					}
				}
			}
		}
		
		if(count($this->files_multiform) > 0)
		{
			foreach($keys_multiform as $key_multiform)
			{
				$key_multiform = $this->crypt->encrypt($key_multiform) ;
				
				foreach($this->files_multiform as $field_form => $type_multiform)
				{
					$name_field = $field_form.'_'.$key_multiform ;
					$id_objet = $this->crypt->decrypt($key_multiform) ;
					$objet = $this->managers->getManagerOf($this->class)->DEF->getUnique($id_objet) ;
						
					if(isset($_FILES[$name_field]) && $_FILES[$name_field]['size'] != 0)
					{
						$dossier = $_SERVER["DOCUMENT_ROOT"].$this->fields[$field_form][4];
				
						$method = 'set'.ucfirst($field_form) ;
							
						$taille = $_FILES[$name_field]['size'];
							
						$extension = explode('.', $_FILES[$name_field]['name']);
						$extension = strtolower($extension[count($extension)-1]);
							
						!empty($this->fields[$field_form][3]) ? $name = $this->fields[$field_form][3] : $name = basename($_FILES[$name_field]['name']);
						$name = strtr($name,
								'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
								'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
						$name = preg_replace('/([^.a-z0-9]+)/i', '', $name);
							
						$error = false ;
							
						$nb_erreur ++ ;
						if($taille > $this->fields[$field_form][5])
						{
							$adds_erreur_multiform[$id_objet][] = $nb_erreur ;
							$error = true ;
						}
							
						$nb_erreur ++ ;
						if(!in_array($extension, $this->fields[$field_form][6]))
						{
							$adds_erreur_multiform[$id_objet][] = $nb_erreur ;
							$error = true ;
						}
							
						$nb_erreur ++ ;
							
						if($error == false && $objet)
						{
							$nom = $objet->$field_form() ;
								
							$nom = explode(".", $nom);
							$nom_unique = $nom[0].'.'.$extension ;
								
							${'tab_'.$field_form}[$id_objet] = $nom_unique ;
								
							if($error == true)
							{
								$adds_erreur_multiform[$id_objet][] = $nb_erreur ;
							}
							else
							{
								$objet->$method($nom_unique) ;
								$this->managers->getManagerOf($this->class)->DEF->save($objet) ;
							}
						}
						else
						{
							${'tab_'.$field_form}[$id_objet] = '' ;
						}
					}
					else 
					{
						$nom = $objet->$field_form() ;
						${'tab_'.$field_form}[$id_objet] = $nom ;
					}
				}
			}
			
			foreach($new_keys_multiform as $id_objet)
			{
				foreach($this->files_multiform as $field_form => $type_multiform)
				{
					$name_field = 'new_'.$field_form.'_'.$id_objet ;
				
					if(isset($_FILES[$name_field]) && $_FILES[$name_field]['size'] != 0)
					{
						$dossier = $_SERVER["DOCUMENT_ROOT"].$this->fields[$field_form][4];
				
						$method = 'set'.ucfirst($field_form) ;
							
						$taille = $_FILES[$name_field]['size'];
							
						$extension = explode('.', $_FILES[$name_field]['name']);
						$extension = strtolower($extension[count($extension)-1]);
							
						!empty($this->fields[$field_form][3]) ? $name = $this->fields[$field_form][3] : $name = basename($_FILES[$name_field]['name']);
						$name = strtr($name,
								'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
								'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
						$name = preg_replace('/([^.a-z0-9]+)/i', '', $name);
							
						$error = false ;
							
						$nb_erreur ++ ;
						if($taille > $this->fields[$field_form][5])
						{
							$adds_erreur_multiform['new_'.$id_objet][] = $nb_erreur ;
							$error = true ;
						}
							
						$nb_erreur ++ ;
						if(!in_array($extension, $this->fields[$field_form][6]))
						{
							$adds_erreur_multiform['new_'.$id_objet][] = $nb_erreur ;
							$error = true ;
						}
							
						$nb_erreur ++ ;
							
						if($error == false)
						{
							$nom_unique = $name.'.'.$extension ;
							
							$id_unique = uniqid('', true) ;
							$nom_unique = $name.'_'.$id_unique.'.'.$extension ;
							
							while(file_exists($dossier.$nom_unique))
							{
								$id_unique = uniqid('', true) ;
							
								$nom_unique = $name.'_'.$id_unique.'.'.$extension ;
							}
				
							${'new_tab'.$field_form}[$id_objet] = $nom_unique ;
				
							if($error == true)
							{
								$adds_erreur_multiform['new_'.$id_objet][] = $nb_erreur ;
							}
						}
						else
						{
							${'new_tab'.$field_form}[$id_objet] = '' ;
						}
					}
				}
			}
		}

		$this->multi = $multi_multiform ;
		
		if(count($this->adds_fields) > 0)
		{
			foreach($keys_multiform as $key_multiform) 
			{
				foreach($this->adds_fields as $add_multiform) 
				{
					$conf = $this->fields[$add_multiform] ;
					
					if($conf_multiform[0] != 'div' && $conf_multiform[0] != '/div' && $conf_multiform[0] != 'p' && $conf_multiform[0] != 'h')
					{
						if($conf[0] != 'hidden' && $conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form' && $conf[0] != 'multiple' && $conf[0] != 'liste')
						{
							if($conf[0] != 'radio')
							{
								foreach($conf[2] as $error_multiform => $msg_multiform)
								{
									$nb_erreur ++ ;
									
									if(isset($_POST[$add_multiform.'_'.$this->crypt->encrypt($key_multiform)]))
									{
										$value_multiform = $_POST[$add_multiform.'_'.$this->crypt->encrypt($key_multiform)] ;
										
										if($error_multiform != 'int' && $error_multiform != 'string' && $error_multiform != 'empty')
										{	
											$validator = new Validator ;
											
											if(!preg_match('/^regex\(/', $error_multiform))
											{
												$method = 'is_'.ucfirst($error);
												
												if(!$validator->$method($value_multiform))
												{
													$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
												}
											}
											else
											{
												$regex = substr($error_multiform, 6, -1);
		
												if (!preg_match($regex,$value_multiform)) 
												{
													$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
												}
											}
										}
										else
										{
											if($error_multiform == 'string')
											{
												if(!is_string($value_multiform))
												{
													$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
												}
											}
											elseif($error_multiform == 'int')
											{
												if(!ctype_digit($value_multiform) && !is_int($value_multiform))
												{
													$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
												}
											}
											elseif(empty($value_multiform) || $value_multiform == '')
											{
												$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
											}
										}
									}
									else
									{
										$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
									}
									
									if((isset($adds_erreur_multiform[$key_multiform]) && !in_array($nb_erreur, $adds_erreur_multiform[$key_multiform])) || !isset($adds_erreur_multiform[$key_multiform]))
									{
										$conf[0] == 'textarea' ? $entity_associate = $conf[5] : $entity_associate = $conf[3] ;
										$RC = new \ReflectionClass($entity_associate);
										$name = $RC->getName();
										
										$target_multiform = explode("\\", $name);
										$target_multiform = $target_multiform[count($target_multiform)-1];
										
										if(!isset($adds_multiform[$key_multiform][$target_multiform]['id']))
										{
											echo 'adds' ;
											$adds_multiform[$key_multiform][$target_multiform]['id'] = $entity_associate->id() ;
										}
										
										$adds_multiform[$key_multiform][$target_multiform][$add_multiform] = $value_multiform ;
									}
								}
							}
							else
							{
								$nb_erreur ++ ;
								
								if(!isset($_POST[$add_multiform.'_'.$this->crypt->encrypt($key_multiform)]))
								{
									$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
								}
								
								if((isset($adds_erreur_multiform[$key_multiform]) && !in_array($nb_erreur, $adds_erreur_multiform[$key_multiform])) || !isset($adds_erreur_multiform[$key_multiform]))
								{
									$value_multiform = $_POST[$add_multiform.'_'.$this->crypt->encrypt($key_multiform)] ;
									
									$entity_associate = $conf[6] ;
									
									$RC = new \ReflectionClass($entity_associate);
									$name = $RC->getName();
									
									$target_multiform = explode("\\", $name);
									$target_multiform = $target_multiform[count($target_multiform)-1];
									
									if(!isset($adds_multiform[$key_multiform][$target_multiform]['id']))
									{
										$adds_multiform[$key_multiform][$target_multiform]['id'] = $entity_associate->id() ;
									}
										
									$adds_multiform[$key_multiform][$target_multiform][$add_multiform] = $value_multiform ;
								}
							}
						}
					}
				}
			}
			
			foreach($new_keys_multiform as $new_key_multiform) 
			{
				foreach($this->adds_fields as $add_multiform) 
				{
					$conf = $this->fields[$add_multiform] ;
					
					if($conf[0] != 'div' && $conf[0] != '/div' && $conf[0] != 'p' && $conf[0] != 'h')
					{
						if($conf[0] != 'hidden' && $conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form' && $conf[0] != 'multiple' && $conf[0] != 'liste')
						{
							if($conf[0] != 'radio')
							{
								foreach($conf[2] as $error_multiform => $msg_multiform)
								{
									$nb_erreur ++ ;
									
									if(isset($_POST['new_'.$add_multiform.'_'.$new_key_multiform]))
									{
										$value_multiform = $_POST['new_'.$add_multiform.'_'.$new_key_multiform] ;
				
										if($error_multiform != 'int' && $error_multiform != 'string' && $error_multiform != 'empty')
										{
											if(!preg_match('/^regex\(/', $error_multiform))
											{
												$method = 'is_'.ucfirst($error_multiform);
												
												if(!$validator->$method($value_multiform))
												{
													$adds_erreur_multiform['new_'.$new_key_multiform][] = $nb_erreur ; 
												}
											}
											else
											{
												$regex = substr($error_multiform, 6, -1);
		
												if (!preg_match($regex,$value_multiform)) 
												{
													$adds_erreur_multiform[$key_multiform][] = $nb_erreur ; 
												}
											}
										}
										else
										{
											if($error_multiform == 'string')
											{
												if(!is_string($value_multiform))
												{
													$adds_erreur_multiform['new_'.$new_key_multiform][] = $nb_erreur ; 
												}
											}
											elseif($error_multiform == 'int')
											{
												if(!ctype_digit($value_multiform) && !is_int($value_multiform))
												{
													$adds_erreur_multiform['new_'.$new_key_multiform][] = $nb_erreur ; 
												}
											}
											elseif(empty($value_multiform) || $value_multiform == '')
											{
												$adds_erreur_multiform['new_'.$new_key_multiform][] = $nb_erreur ; 
											}
										}
									}
									else
									{
										$adds_erreur_multiform['new_'.$new_key_multiform][] = $nb_erreur ; 
									}
									
							
									$conf[0] == 'textarea' ? $entity_associate = $conf[5] : $entity_associate = $conf[3] ;
									
									$RC = new \ReflectionClass($entity_associate);
									$name = $RC->getName();
									
									$target_multiform = explode("\\", $name);
									$target_multiform = $target_multiform[count($target_multiform)-1];
									
									if(!isset($adds_multiform[$new_key_multiform][$target_multiform]['id'])) $adds_multiform[$new_key_multiform][$target_multiform]['id'] = $entity_associate->id() ;
									
									$adds_multiform[$new_key_multiform][$target_multiform][$add_multiform] = $value_multiform ;
									
								}
							}
							else
							{
								$nb_erreur ++ ;
								
								if(isset($_POST['new_'.$add_multiform.'_'.$new_key_multiform]))
								{
									$value_multiform = $_POST['new_'.$add_multiform.'_'.$new_key_multiform] ;
									$entity_associate = $conf[6] ;
									$RC = new \ReflectionClass($entity_associate);
									$name = $RC->getName();
									
									$target_multiform = explode("\\", $name);
									$target_multiform = $target_multiform[count($target_multiform)-1];
									
									if(!isset($adds_multiform[$new_key_multiform][$target_multiform]['id'])) $adds_multiform[$new_key_multiform][$target_multiform]['id'] = $entity_associate->id() ;
									
									$adds_multiform[$new_key_multiform][$target_multiform][$add_multiform] = $value_multiform ;
								}
								else
								{
									$adds_erreur_multiform['new_'.$new_key_multiform][] = $nb_erreur ; 
								}
							}
						}
					}
				}
			}
		}
		
		$this->adds = $adds_multiform ;
		
		foreach($this->entities as $entity_id_multiform)
		{
			$old_entity_multiform[] = $entity_id_multiform->id() ;	
		}
		
		$stop_multiform = false ;

		foreach($old_entity_multiform as $entity_id_multiform)
		{
			if(in_array($entity_id_multiform, $keys_multiform))
			{
				foreach($champs_multiform as $champ_multiform => $field_multiform)
				{
					foreach(${$champ_multiform} as $key_multiform => $val_multiform)
					{
						if($key_multiform == $entity_id_multiform)
						{
							$$field_multiform = $this->crypt->decrypt($val_multiform) ;
						}
					}
					
					if(!isset($$field_multiform))
					{
						$stop_multiform = true ;
					}
				}
				
				if($stop_multiform == false)
				{
					foreach($champs_multiform as $champ_multiform => $field_multiform)
					{
						$array_entity_multiform[$field_multiform] = $$field_multiform ;
					}
					
					if(count($add) > 0 )
					{
						foreach($add as $attribut => $val)
						{
							$array_entity_multiform[$attribut] = $val ;
						}
					}
					
					$array_entity_multiform['id'] = $entity_id_multiform ;
					$entity_multiform = new $class_multiform($array_entity_multiform) ;

					if ($entity_multiform->isValid() && !isset($adds_erreur_multiform[$entity_id_multiform]))
					{
						if($validate === false) 
						{
							if(method_exists($this->managers->getManagerOf($this->entity), 'save'))
							{	
								$this->managers->getManagerOf($this->entity)->save($entity_multiform, $this->app->config()->getGlobal('encrypt_key')) ;
							}
							else
							{
								$this->managers->getManagerOf($this->entity)->DEF->save($entity_multiform, $this->app->config()->getGlobal('encrypt_key')) ;
							}
							
							foreach($this->files_multiform as $field_form => $type_multiform)
							{
								$encrypt_id = $this->crypt->encrypt($entity_id_multiform);
								
								$name_field = $field_form.'_'.$encrypt_id ;

								if(isset($_FILES[$name_field]))
								{
									$dossier = $_SERVER["DOCUMENT_ROOT"].$this->fields[$field_form][4];
									$name_file = $entity_multiform[$field_form] ;
									move_uploaded_file($_FILES[$name_field]['tmp_name'], $dossier.$name_file) ;
								}
							}
						}
						else
						{
							$multiform_validate_entities[] = $entity_multiform ; 
						}
					}
					else
					{
						${$erreurs_multiform}[$entity_id_multiform] = $entity_multiform->erreurs();
						
						$erreur_multiform ++ ;
					}
					
					$modify_multiform[] = $entity_multiform ;
				}
				
				if(isset($adds_erreur_multiform[$entity_id_multiform]))
				{
					if(isset(${$erreurs_multiform}[$entity_id_multiform]))
					{
						${$erreurs_multiform}[$entity_id_multiform] = array_merge(${$erreurs_multiform}[$entity_id_multiform], $adds_erreur_multiform[$entity_id_multiform]) ;
					}
					else
					{
						${$erreurs_multiform}[$entity_id_multiform] = $adds_erreur_multiform[$entity_id_multiform] ;
					}
				}
				
				if(isset($liste_erreur_multiform[$entity_id_multiform]))
				{
					if(isset(${$erreurs_multiform}[$entity_id_multiform]))
					{
						${$erreurs_multiform}[$entity_id_multiform] = array_merge(${$erreurs_multiform}[$entity_id_multiform], $liste_erreur_multiform[$entity_id_multiform]) ;
					}
					else
					{
						${$erreurs_multiform}[$entity_id_multiform] = $liste_erreur_multiform[$entity_id_multiform] ;
					}
				}
			}
			elseif(ctype_digit($entity_id_multiform) && $validate == false)
			{
				if(count($this->files_multiform) > 0)
				{
					$entity_multiform = $this->managers->getManagerOf($this->entity)->DEF->getUnique($entity_id_multiform) ;
					
					foreach($this->files_multiform as $field_form => $type_multiform)
					{
						$dossier = $_SERVER["DOCUMENT_ROOT"].$this->fields[$field_form][4];
						$name_file = $entity_multiform[$field_form] ;
						
						if(file_exists($dossier.$name_file))
						{
							unlink($dossier.$name_file) ;
						}
					}
				}
				
				method_exists($this->managers->getManagerOf($this->entity), 'delete') ? $this->managers->getManagerOf($this->entity)->delete($entity_id_multiform) : $this->managers->getManagerOf($this->entity)->DEF->delete($entity_id_multiform) ;
			}
		}
		
		/*echo "tab " ;
		var_dump($entity_multiform->erreurs()) ;*/

		if(count($new_keys_multiform) > 0)
		{
			foreach($new_keys_multiform as $key_multiform)
			{
				foreach($new_champs_multiform as $new_tab_multiform => $field_multiform)
				{
					if(!isset($count_multiform)) 
					{
						$count_multiform = count($$new_tab_multiform) ;
					}
					else
					{
						if($count_multiform != count($$new_tab_multiform))
						{
							throw new \InvalidArgumentException('Il n\'y a pas le meme nombre d\'elements pour chaque entites');
						}
					}
				}
				
				foreach($new_champs_multiform as $new_tab_multiform => $field_multiform)
				{
					$$field_multiform = ${$new_tab_multiform}[$key_multiform] ;
					
					if(isset($this->listes_multiform[$field_multiform]) && $this->listes_multiform[$field_multiform] == 'liste')
					{ 
						$$field_multiform = ${$field_multiform}[0] ;
					}
					
					$$field_multiform = $this->crypt->decrypt($$field_multiform) ;
					$array_new_entity_multiform[$field_multiform] = $$field_multiform ;
				}
				
				if(count($add) > 0 )
				{
					foreach($add as $attribut => $val)
					{
						$array_new_entity_multiform[$attribut] = $val ;
					}
				}
				
				$entity_multiform = new $class_multiform($array_new_entity_multiform) ;
				
				if($entity_multiform->isValid() && count($adds_erreur_multiform) == 0)
				{
					if($validate === false) 
					{
						method_exists($this->managers->getManagerOf($this->entity), 'save') ? $this->managers->getManagerOf($this->entity)->save($entity_multiform, $this->app->config()->getGlobal('encrypt_key')) : $this->managers->getManagerOf($this->entity)->DEF->save($entity_multiform, $this->app->config()->getGlobal('encrypt_key')) ;
						
						$id_entity_multiform = $this->managers->getManagerOf($this->entity)->dao->lastInsertId();
						
						$entity_multiform->setId($id_entity_multiform) ;
						
						if(isset($this->multi[$key_multiform]))
						{
							foreach($this->multi[$key_multiform] as $associate_name_multiform => $associate_multiform)
							{
								foreach($associate_multiform as $id_associate_multiform)
								{
									method_exists($this->managers->getManagerOf($this->entity), 'associate') ? $this->managers->getManagerOf($this->entity)->associate($id_entity_multiform, $id_associate_multiform, $associate_name_multiform) : $this->managers->getManagerOf($this->entity)->DEF->associate($id_entity_multiform, $id_associate_multiform, $associate_name_multiform) ;
								}
							}
						}
						
						foreach($this->files_multiform as $field_form => $type_multiform)
						{
							$name_field = 'new_'.$field_form.'_'.$key_multiform ;
							$dossier = $_SERVER["DOCUMENT_ROOT"].$this->fields[$field_form][4];
							$name_file = $entity_multiform[$field_form] ;
							move_uploaded_file($_FILES[$name_field]['tmp_name'], $dossier.$name_file) ;
						}
						
						if(isset($this->adds[$key_multiform]))
						{
							//echo 1 ;	
						}
					}
					else
					{
						if(!preg_match('/^new_/', $entity_multiform->id()))
						{
							$id_entity_multiform = 'new_'.rand(0,100).rand(0,999999) ;
							$entity_multiform->setId($id_entity_multiform);
						}
					}

					$new_multiform[] = $entity_multiform ;
				}
				else
				{
					if($entity_multiform->id() != null) 
					{
						$id_entity_multiform = $entity_multiform->id() ;
					}
					else
					{
						$id_entity_multiform = 'new_'.rand(0,100).rand(0,999999) ;
						$entity_multiform->setId($id_entity_multiform);
					}
					
					${$new_erreur_multiform}[$id_entity_multiform] = $entity_multiform->erreurs();
					$new_multiform[] = $entity_multiform ;
					$erreur_multiform ++ ;
				}

				if(isset($liste_erreur_multiform[$key_multiform]))
				{
					if(isset(${$new_erreur_multiform}[$id_entity_multiform]))
					{
						${$new_erreur_multiform}[$id_entity_multiform] = array_merge(${$new_erreur_multiform}[$id_entity_multiform], $liste_erreur_multiform[$key_multiform]) ;
					}
					else
					{
						${$new_erreur_multiform}[$id_entity_multiform] = $liste_erreur_multiform[$key_multiform] ;
					}
				}
				
				if(isset($adds_erreur_multiform['new_'.$key_multiform]))
				{
					if(isset(${$new_erreur_multiform}[$id_entity_multiform]))
					{
						${$new_erreur_multiform}[$id_entity_multiform] = array_merge(${$new_erreur_multiform}[$id_entity_multiform], $adds_erreur_multiform['new_'.$key_multiform]) ;
					}
					else
					{
						${$new_erreur_multiform}[$id_entity_multiform] = $adds_erreur_multiform['new_'.$key_multiform] ;
					}
				}
				
				if(isset($this->multi[$key_multiform]))
				{
					$this->multi[$id_entity_multiform] = $this->multi[$key_multiform] ;
					unset($this->multi[$key_multiform]);
				}
				
				if(isset($this->adds[$key_multiform]))
				{
					$this->adds[$id_entity_multiform] = $this->adds[$key_multiform] ;
					unset($this->adds[$key_multiform]);
				}
			}
		}
		
		$entities = array_merge($modify_multiform, $new_multiform) ;
		
		$this->erreurs = $$erreurs_multiform ;
		$this->new_erreurs = $$new_erreur_multiform ;
		
		if($erreur_multiform === 0)
		{
			if($validate === false)
			{
				$this->valide == '' ? $this->app->user()->setFlash($this->entity.' sauvegardé') : $this->app->user()->setFlash($this->valide) ;
			}
			elseif($validate === true)
			{
				return $entities ;
			}
			elseif($validate === 1)
			{
				$this->app->user()->setFlash('Il n\'y a pas d\'erreurs') ;
			}
		}
		elseif($validate === false || $validate === 1)
		{
			if($erreur_multiform === 1)
			{
				$this->app->user()->setFlash('1 formulaire présente une ou plusieurs erreurs');
			}
			else
			{
				$this->app->user()->setFlash($erreur_multiform.' formulaires présentent des erreurs');
			}
		}
		else
		{
			return false ;	
		}
		
		$this->entities = $entities ;
		
		$this->token = $token_multiform ;
		
		return $this ;
	}
	
	public function newToken()
	{
		$id = $this->app->user()->key() ;
		$this->token = $this->app->user()->generateToken($this->entity.'_'.$id) ;
	}
	
	public function adds()
	{
		return $this->adds;
	}
	
	public function entity()
	{
		return $this->entity;
	}
	
	public function multi()
	{
		return $this->multi;
	}
	
	public function vars()
	{
		return $this->vars;
	}
	
	public function fields()
	{
		return $this->fields;
	}
	
	public function entities()
	{
		return $this->entities;
	}
	
	public function spacename()
	{
		return $this->namespace;
	}
	
	public function nom()
	{
		return $this->nom;
	}
	
	public function classe()
	{
		return $this->class;
	}
	
	public function class_form()
	{
		return $this->class_form;
	}
	
	public function valide()
	{
		return $this->valide;
	}
	
	public function objet()
	{
		return $this->objet;
	}
	
	public function SetEntities(array $entities)
	{
		$this->entities = $entities ;
	}
}