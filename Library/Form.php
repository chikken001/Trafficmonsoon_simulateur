<?php
namespace Library;

use \Library\Crypt ;
use \Library\Validator ;

class Form
{
	protected $token ;
	protected $app ;
	protected $form = '' ;
	protected $namespace ;
	protected $fields ;
	protected $entity ;
	protected $managers ;
	protected $objet ;
	protected $erreurs = array() ;
	protected $valide = '' ;
	protected $crypt ;
	protected $multiform ='' ;
	protected $arg_multiform ='' ;
	protected $bol = false ;
	protected $vars = array() ;
	protected $var_submit = array() ;
    protected $fichiers = array() ;
	protected $adds_fields = array() ;
	
	public function __construct($entity, array $fields, \Library\Application $app, \Library\Managers $managers)
	{
		$this->crypt = new Crypt ;
		$this->app = $app ;
		$this->managers = $managers ;
		$bol = false ;
		$i = 0 ;
		$nb_erreur = 10000 ;
		$types = array('form','text','textarea','submit','button','hidden','tel','email','password','date','color','datetime','datetime-local','month','number','range','search','time','url','week','multiple','liste','multiform','checkbox','radio','file') ;
		$attributes = array('accesskey','class','contenteditable','contextmenu','dir','draggable','dropzone','hidden','id','lang','spellcheck','style','tabindex','title','translate','placeholder') ;
		$form = '' ;
        $form_file = '' ;
        
		if(is_object($entity))
		{
			$this->objet = $entity ;
			
			$RC = new \ReflectionClass($entity);
			$this->namespace = $RC->getName();
	
			$entity = explode("\\", $this->namespace);
			$this->entity = $entity[count($entity)-1];
			
			$entity = new $this->namespace ; 
		
			$attributs = $entity->getVars();
			
			foreach ($attributs as $nom => $valeur) 
			{
				$this->vars[] = $nom ;
			}
		}
		elseif(is_array($entity))
		{
			$this->objet = $entity ;
			$this->entity = '@Formulaire' ;
		}
		else
		{
			throw new \InvalidArgumentException('L\'entite $entity doit etre un objet ou un tableau');
		}
		
		foreach($fields as $field => $conf)
		{
			if(is_string($field) && $conf[0] != 'div' && $conf[0] != '/div' && $conf[0] != 'p' && $conf[0] != 'h')
			{
				if($field != 'valide_form')
				{
					if($conf[0] == 'multiform')
					{
						if(!isset($conf[2]) || empty($conf[2]) || !is_string($conf[2]))
						{
							throw new \InvalidArgumentException('L\'argument de liaison du champ multiform '.$field.' est invalide ou vide');
						}
						
						if(!$conf[1] instanceof \Library\MultiForm) 
						{
							throw new \InvalidArgumentException("l'objet doit etre un multiform pour $field ");
						}
						else
						{
							$vars = $conf[1]->vars() ;
							if(!in_array($conf[2], $vars)) throw new \InvalidArgumentException("L'argument de liaison du champ multiform '$field' n'est pas un argument de l'entite du multiformulaire : ".$conf[2]);
						}
						
						$this->multiform = $conf[1] ;
						$this->arg_multiform = $conf[2] ;
					}
					elseif(in_array($conf[0], $types))
					{
						if($conf[0] == 'liste' || $conf[0] == 'multiple' || $conf[0] == 'radio' || $conf[0] == 'checkbox')
						{
							$nb_erreur ++ ;
							
							if(is_object($this->objet) && $conf[0] == 'radio')
							{
								if(isset($conf[6]) && is_object($conf[6]))
								{
									$this->adds_fields[] = $field ;	
								}
								elseif(!in_array($field, $this->vars))
								{
									$debug = debug_backtrace();
									throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Le champ '.$field.' specifie dans $fields n\'est pas un attribut de l\'objet : '.$this->entity);
								}
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
									$boll = false ;
									
									foreach ($vars as $nom => $valeur) 
									{
										if(isset($conf[5]) && $nom == $conf[5])
										{
											$boll = true ;	
										}
									}
									
									if($boll == false)
									{
										isset($conf[5]) ? $conf = $conf[5] : $conf = '' ; 
										throw new \InvalidArgumentException('L\'attribut de reference ('.$conf.') specifie a '.$field.' n\'est pas un attribut de l\'objet : '.$name);
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
										throw new \InvalidArgumentException('L\'attribut de refrence specifie a '.$field.' est invalide : '.$conf[5]);
									}
								}
								elseif(isset($conf[5]) && !empty($conf[5]))
								{
									throw new \InvalidArgumentException('L\'attribut de refrence specifie a '.$field.' doit etre vide si vous n\'utilisez pas des objets');
								}
							}
							
							if($conf[0] == 'multiple' || $conf[0] == 'checkbox')
							{
								if(isset($conf[6]) && !empty($conf[6]) && (!is_int($conf[6]) || $conf[6] < 0))
								{
									throw new \InvalidArgumentException('Le nombre d\'elements minimum a selectionner specifie a '.$field.' est invalide : '.$conf[1]);
								}
							}
							
							if($this->entity === '@Formulaire' && ((count($conf[4]) === 0 || !isset($this->objet[$field]) || count($this->objet[$field]) === 0) && ((isset($conf[6]) && $conf[6] > 0) || !isset($conf[6]))))
							{
								$this->bol = true ;
								$this->erreurs[] = $nb_erreur ;	
							}
							
						}
						elseif($conf[0] != 'hidden' && $conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form')
						{
							$i ++ ;
                            
							if(is_object($this->objet))
							{
								if(($conf[0] != 'textarea' && isset($conf[3]) && is_object($conf[3])) || ($conf[0] == 'textarea' && isset($conf[5]) && is_object($conf[5])))
								{
									$this->adds_fields[] = $field ;	
								}
								elseif(!in_array($field, $this->vars))
								{
									$debug = debug_backtrace();
									throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Le champ '.$field.' specifie dans $fields n\'est pas un attribut de l\'objet : '.$this->entity);
								}
							}
							
							if(isset($conf[1]) && !is_string($conf[1]) && !empty($conf[1]))
							{
								throw new \InvalidArgumentException('Le Label specifie a '.$field.' est invalide : '.$conf[1]);
							}
							
							if(isset($conf[2]) && !is_array($conf[2]) && !empty($conf[2]))
							{
								throw new \InvalidArgumentException('Le tableau de messages d\'erreurs specifie a '.$field.' est invalide');
							}
							else
							{
								foreach($conf[2] as $error => $msg)
								{
									if(is_object($this->objet) && !in_array($field, $this->adds_fields))
									{
										$const = strtoupper($field.'_'.$error) ;
										
										if(!defined($this->namespace."::$const"))
										{
											throw new \InvalidArgumentException('L\'erreur specifie a "'.$field.'" ('.$const.') n\'est pas defini a l\'entite : '.$this->entity);
										}
									}
									else
									{
										$validator = new Validator ;
										
										if($error != 'int' && $error != 'string' && $error != 'empty' && !preg_match('/^regex\(/', $error))
										{  
											$method = 'is_'.ucfirst($error);
											
											if(!is_callable(array($validator, $method)))
											{
												throw new \InvalidArgumentException('Le validator ne contient pas la methode : '.$method);
											}
										}
										elseif(preg_match('/^regex\(/', $error) && $error[strlen($error)-1] !== ')')
										{
											throw new \InvalidArgumentException('L\'appel de la regex doit etre sous la forme : regex(REGEX) pour "'.$field.'"');
										}
									}
									
									if(!isset($msg) || !is_string($msg))
									{
										throw new \InvalidArgumentException('Le message d\'erreur specifie a "'.$field.'" pour l\'erreur '.$error.' est invalide : '.$msg);
									}
									
									if(isset($this->objet[$field]) && $this->entity == '@Formulaire')
									{
										if($error != 'int' && $error != 'string' && $error != 'empty')
										{		
											if(!preg_match('/^regex\(/', $error))
											{
												$method = 'is_'.ucfirst($error);
												
												if(!$validator->$method($this->objet[$field]))
												{
													$this->bol = true ;
													$this->erreurs[] = $i ;
												}
											}
											else
											{
												$regex = substr($error_multiform, 6, -1);
		
												if (!preg_match($regex,$this->objet[$field])) 
												{
													$this->bol = true ;
													$this->erreurs[] = $i ;
												}
											}
										}
										else
										{
											if($error == 'string')
											{
												if(!is_string($this->objet[$field]))
												{
													$this->bol = true ;
													$this->erreurs[] = $i ;
												}
											}
											elseif($error == 'int')
											{
												if(!ctype_digit($this->objet[$field]) && !is_int($this->objet[$field]))
												{
													$this->bol = true ;
													$this->erreurs[] = $i ;
												}
											}
											elseif(empty($this->objet[$field]) || $this->objet[$field] == '')
											{
												$this->bol = true ;
												$this->erreurs[] = $i ;
											}
										}
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
                                
                                $form_file = 'enctype="multipart/form-data"' ;
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
								
							if(isset($arguments) && !is_array($arguments))
							{
                                $debug = debug_backtrace();
								throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Le tableau d\'arguments specifie a '.$field.' est invalide');
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
						}
						elseif($conf[0] == 'hidden' && in_array($field, $this->vars))
						{
							$conf[1] = true ;
						}
						elseif($conf[0] == 'submit')
						{
                            if(empty($field) || !is_string($field))
                            {
                                throw new \InvalidArgumentException('Le nom du boutton submit est invalide');
                            }
 
                            if(!isset($conf[1]) || !is_string($conf[1]))
                            {
                                throw new \InvalidArgumentException('La valeur pour le bouton submit est invalide');
                            }
                            
                            if(empty($field))
                            {
                                throw new \InvalidArgumentException('Le nom du bouton submit ne peut pas être vide') ;
                            }
                            else
                            {
                                $this->var_submit[] = $field ;
                            }
                            
                            if(isset($conf[2]) && !is_string($conf[2]))
                            {
                                throw new \InvalidArgumentException('La class specifie au bouton submit est invalide');
                            }
							
							if(isset($conf[3]) && !is_string($conf[3]))
                            {
                                throw new \InvalidArgumentException('La class specifie au bouton submit est invalide');
                            }
						}
						elseif($conf[0] == 'form')
						{
							$form= '<form action="" method="post" name="'.$field.'"' ;
							
							if(isset($conf[1]) && is_string($conf[1]))
							{
								$form .= ' class="'.$conf[1].'"' ;
							}
						}
					}
					else
					{
						$debug = debug_backtrace();
						throw new \InvalidArgumentException('Ligne '.$debug[0]["line"].' dans '.$debug[0]["file"].' : Le type de champ "'.$conf[0].'" est invalide');
					}
				}
				else
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
        !empty($form) ? $this->form = $form.$form_file.'>' : $this->form = '<form action="" method="post" name="'.$this->entity.'" '.$form_file.'>' ;
        count($this->var_submit) == 0 ? $this->var_submit[0] = 'Enregistrer_'.$this->entity : $this->var_submit = $this->var_submit ;
			
		$id = $app->user()->key() ;
		$is_submit = false ;
        
        foreach($this->var_submit as $submit)
        {
            isset($_POST[$submit]) ? $is_submit = true : false ;
        }
        
		if($is_submit === false)
		{
			$this->token = $app->user()->generateToken($this->entity.'_'.$id) ;
		}
	}
	
	public function form ()
	{
		$token = '<input type="hidden" name="token" value="'.$this->token.'"/>' ;
		
		$formulaire = $this->form ;
		$i = 0 ;
		
		$nb_erreur = 10000 ;
		$tab_liste = array() ;
		$multiform ='';
		
		if($this->entity != '@Formulaire'){ $prefix_field = $this->entity.'_' ; $this->bol = true ;}else{$prefix_field = '' ; }
		
		foreach($this->fields as $field => $conf)
		{
			$value ='' ;
			if($field != 'valide_form' && $conf[0] != 'div' && $conf[0] != '/div' && $conf[0] != 'multiform' && $conf[0] != 'h' && $conf[0] != 'p')
			{
				$errors = array () ;
				
				if($conf[0] == 'liste' || $conf[0] == 'multiple' || $conf[0] == 'radio' || $conf[0] == 'checkbox')
				{
					$field_champ = '' ;
					$nb_erreur ++ ;
					
					isset($conf[1]) ? $field_label = '<label for="'.$prefix_field.$field.'">'.$conf[1].'</label>' : $field_label = '' ;

                    isset($conf[2]) ? $field_erreur = '<p class="msg_erreur">'.$conf[2].'</p>' : $field_erreur = '<p class="msg_erreur">'.$field.' Invalide</p>' ;
                    
					if($conf[0] == 'liste' || $conf[0] == 'multiple')
					{
						$conf[0] == 'multiple' ? $multiple = 'multiple="multiple"' : $multiple = '' ;
						$field_champ = '<select name="'.$prefix_field.$field.'[]" '.$multiple.'>' ;
					}
					
					$keys_form = array_keys($conf[4]); 						
					
					if(count($conf[4]) > 0)
					{
						if(isset($this->objet['id']) && !empty($this->objet['id']) && is_object($conf[4][$keys_form[0]]))
						{
							$RC = new \ReflectionClass($conf[4][$keys_form[0]]);
							$name = $RC->getName();
							
							$exp = explode("\\", $name);
							$name = strtolower($exp[count($exp)-1]);
							
							if($conf[3] === 'many')
							{
								$liste = $this->managers->getManagerOf($this->entity)->DEF->getAssociate($this->objet['id'], $name) ;
									
								foreach($liste as $entity)
								{
									$tab_liste[] = $entity[$name] ;
								}
							}
							elseif($conf[3] === 'one')
							{
								$tab_liste[] = $this->managers->getManagerOf($this->entity)->DEF->getUnique($this->objet['id'])->$field() ;
							}
							else
							{
								$tab_liste[] = $this->crypt->decrypt($entity[$field]) ;
							}
						}
						elseif(isset($_POST[$prefix_field.$field]))
						{
							$liste = $_POST[$prefix_field.$field] ;
							
							if($conf[3] === 'many')
							{
								foreach($liste as $liste_id)
								{
									$tab_liste[] = $this->crypt->decrypt($liste_id) ;
								}
								
							}
							elseif($conf[3] === 'one')
							{
								$tab_liste[] = $this->crypt->decrypt($liste[0]) ;
							}
						}
					}
					
					if($conf[0] == 'liste' && isset($conf[6]) && $conf[6] == 0)
					{
						$field_champ .= '<option value=""> </option>' ;
					}
					
                    $i = 0 ;
                    
					foreach($conf[4] as $index => $entity)
					{
						if(is_object($entity))
						{
							if($this->entity == '@Formulaire')
							{
								if(isset($this->objet[$field]))
								{
									foreach($this->objet[$field] as $value)
									{
										$tab_liste[] = $this->crypt->decrypt($value) ;
									}
								}
							}
							
							if($conf[0] == 'liste' || $conf[0] == 'multiple')
							{
								in_array($entity->id(), $tab_liste) ? $selected = 'selected' : $selected = '' ;
								$field_champ .= '<option value="'.$this->crypt->encrypt($entity->id()).'" '.$selected.'>'.$entity->$conf[5]().'</option>' ;
							}
							elseif($conf[0] == 'radio')
							{
								if(in_array($entity->id(), $tab_liste) || $i == 0)
                                {
                                    $selected = 'checked' ;
                                }
                                else
                                {
                                    $selected = '' ;
                                }
								
								$field_champ .= '<input type="radio" name="'.$prefix_field.$field.'" value="'.$this->crypt->encrypt($entity->id()).'" '.$selected.'>'.$entity->$conf[5]() ;
							}
						}
						else
						{
							$selected = '' ;
							
							if($conf[0] == 'liste' || $conf[0] == 'multiple')
							{
								if(isset($this->objet[$field]) && $this->objet[$field] == $entity || (is_array($this->objet)[$field] && in_array($entity, $this->objet[$field]))){$selected = 'selected';}
								$field_champ .= '<option value="'.$entity.'"'.$selected.'>'.$entity.'</option>' ;
							}
							elseif($conf[0] == 'radio')
							{
								if($i == 0 ||(isset($this->objet[$field]) && $this->objet[$field] == $index) || (is_array($this->objet)[$field] && in_array($index, $this->objet[$field])) || (isset($this->objet->$field) && $this->objet->$field == $index)){$selected = 'checked';}
								$field_champ .= '<input id="'.$prefix_field.$field.'_'.$entity.'" type="radio" name="'.$prefix_field.$field.'" value="'.$index.'"'.$selected.'><label for='.$prefix_field.$field.'_'.$entity.'>'.$entity.'</label>' ;
							}
						}
                        $i ++ ;
					}
					
					if($conf[0] == 'liste' || $conf[0] == 'multiple')
					{
						$field_champ .= '</select>' ;
					}
					
                    if($conf[0] != 'radio')
                    {
                        if (count($this->erreurs) > 0 && in_array($nb_erreur, $this->erreurs) && $this->bol === true) 
                        {
                            for($i = 0 ; $i < count($this->var_submit) ; $i++)
                            {
                                if(isset($_POST[$this->var_submit[$i]]))
                                {
                                    $formulaire .= $field_erreur ;
                                    $i = count($this->var_submit) ;
                                }
                            }
                        }
                    }
                    elseif(isset($conf[2]) && is_array($conf[2]))
                    {
                        foreach($conf[2] as $error => $msg)
                        {
                            if(is_object($this->objet))
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
                                    
                                    if (count($this->erreurs) > 0 && in_array($value, $this->erreurs)) 
                                    {
                                        $field_erreur .= '<p class="msg_erreur">'.$msg_error.'</p>';
                                        $formulaire .= $field_erreur ;
                                    }
                                    
                                    $errors[] = strtoupper($error) ;
                                }
                            }
                            else
                            {
                                if(isset($this->objet[$field]))
                                {
                                    if($this->bol === true && in_array($i, $this->erreurs))
                                    {
                                        $msg != '' ? $msg_error = $msg : $msg_error = $field.' erreur' ;
                                        $field_erreur .= '<p class="msg_erreur">'.$msg_error.'</p>';
                                        $formulaire .= $field_erreur ;
                                    }
                                }
                            }
                        }
                    }
					
					$formulaire .= $field_label.$field_champ;
					
				}
				elseif($conf[0] != 'hidden' && $conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form')
				{
					$i ++ ;
					$field_label = '' ;
					$field_champ = '' ;
					$field_erreur = '' ;
					
					foreach($conf[2] as $error => $msg)
					{
						if(is_object($this->objet) && !in_array($field, $this->adds_fields))
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
								
								if (count($this->erreurs) > 0 && in_array($value, $this->erreurs)) 
								{
									$field_erreur .= '<p class="msg_erreur">'.$msg_error.'</p>';
									$formulaire .= $field_erreur ;
								}
								
								$errors[] = strtoupper($error) ;
							}
						}
						elseif((isset($this->objet[$field]) && $this->bol === true && in_array($i, $this->erreurs)) || in_array($field, $this->adds_fields))
						{
							$error = true ;
							
							if(in_array($field, $this->adds_fields))
							{
								$nb_erreur ++ ;
								
								if(!in_array($nb_erreur, $this->erreurs))
								{
									$error = false ;
								}
							}
							
							if($error === true)
							{
								$msg != '' ? $msg_error = $msg : $msg_error = $field.' erreur' ;
								$field_erreur .= '<p class="msg_erreur">'.$msg_error.'</p>';
								$formulaire .= $field_erreur ;
							}
						}
					}
                    
                    if($conf[0] == 'file')
                    {
                        for($a = 1 ; $a < 4 ; $a++)
                        {
                            $nb_erreur ++ ;
                            
                            if($a == 1)
                            {
                                $field_erreur .= '<p class="msg_erreur">Le fichier est trop volumineux</p>' ;
                            }
                            elseif($a == 2)
                            {
                                $field_erreur .= '<p class="msg_erreur">Le fichier n\'est pas valide</p>' ;
                            }
                            elseif($a == 3)
                            {
                                $field_erreur .= '<p class="msg_erreur">Erreur lors de l\'upload du fichier</p>' ;
                            }
                            
                            if (count($this->erreurs) > 0 && in_array($nb_erreur, $this->erreurs) && $this->bol === true) 
                            {
                                for($i = 0 ; $i < count($this->var_submit) ; $i++)
                                {
                                    if(isset($_POST[$this->var_submit[$i]]))
                                    {
                                        $formulaire .= $field_erreur ;
                                        $i = count($this->var_submit) ;
                                    }
                                }
                            }
                        }
                    }
                    
					if(is_object($this->objet) && !in_array($field, $this->adds_fields))
					{
						$const = strtoupper($field.'_INVALIDE') ;
						$value = constant($this->namespace."::$const");
						
						if(!in_array('INVALIDE', $errors) && count($this->erreurs) > 0 && in_array($value, $this->erreurs))
						{
							$msg_error = 'Invalide' ;
							$field_erreur = '<p class="msg_erreur">'.$msg_error.'</p>';
							$formulaire .= $field_erreur ;
						}
						
						$const = strtoupper($field.'_INDISPONIBLE') ;
						
						if(defined($this->namespace."::$const"))
						{
							$value = constant($this->namespace."::$const");
							
							if(!in_array('INDISPONIBLE', $errors) && count($this->erreurs) > 0 && in_array($value, $this->erreurs))
							{
								$msg_error = 'Déjà utilisé' ;
								$field_erreur = '<p class="msg_erreur">'.$msg_error.'</p>';
								$formulaire .= $field_erreur ;
							}
						}
						
						$value = $this->objet[$field] ;
					}
					elseif(in_array($field, $this->adds_fields))
					{
						/*if(isset($_POST[$prefix_field.$field]))
						{
							$value = $_POST[$prefix_field.$field] ;	
						}
						else
						{*/
							$conf[0] == 'textarea' ? $objet = $conf[5] : $objet = $conf[3] ;
							$RC = new \ReflectionClass($objet);
							$name = $RC->getName();
							$name = explode("\\", $name);
							$name = $name[count($name)-1];
						
							method_exists($this->managers->getManagerOf($name), 'getArgAssociate') ? $value = $this->managers->getManagerOf($name)->getArgAssociate($field,$this->objet->id() , $this->entity, $objet->id()) : $value = $this->managers->getManagerOf($name)->DEF->getArgAssociate($field, $this->objet->id(), $this->entity, $objet->id()) ;
						//}
					}
					else
					{
						if(isset($this->objet[$field]))
						{
							$value = $this->objet[$field] ;
						}
					}
					
					if(!empty($conf[1]))
					{
						$field_label = '<label for="'.$prefix_field.$field.'">'.$conf[1].'</label>' ;
					}
					
					if($conf[0] == 'textarea')
					{
						$field_champ = '<textarea name="'.$prefix_field.$field.'"' ;
						
						if(isset($conf[3])){ $field_champ .= ' rows ="'.$conf[3].'"' ; }
						if(isset($conf[4])){ $field_champ .= ' cols ="'.$conf[4].'"' ; } 
						
						if(isset($conf[6]))
						{
							foreach($conf[6] as $index => $value)
							{
								$field_champ .= ' '.$index.' ="'.$value.'"' ;
							}
						}
						
						$field_champ .= '>'.$value.'</textarea>' ;
					}
					elseif($conf[0] == 'password')
					{
						$field_champ = '<input type="password" name="'.$prefix_field.$field.'" value=""' ; 
						
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
                        $field_champ = '<input type="file" name="'.$prefix_field.$field.'">' ;
                        $path = str_replace(PATH, $_SERVER["DOCUMENT_ROOT"], $value) ;
                        
                        if(!empty($value) && file_exists ($path))
                        {
                            $field_champ .= '<p>fichier actuel :<p>' ;
                            
                            if(exif_imagetype($path) && isset($conf[7]) && $conf[7] == 1)
                            {
                                $field_champ .= '<img src="'.$value.'">' ;
                            }
                            else
                            {
                                $field_champ .= '<p>'.$value.'</p>' ;
                            }
                        }
                    }
					else
					{
						$field_champ = '<input type="'.$conf[0].'" name="'.$prefix_field.$field.'" value="'.$value.'"' ;
						
						if(isset($conf[4]) && is_array($conf[4]))
						{
							foreach($conf[4] as $index => $value)
							{
								$field_champ .= ' '.$index.' ="'.$value.'"' ;
							}
						}
						
						$field_champ .= '/>' ; 
					}
					
					$formulaire .= $field_label.$field_champ;
					
					if($conf[0] == 'password' && is_object($this->objet) && !in_array($field, $this->adds_fields))
					{
						if(isset($conf[3]) && !is_bool($conf[3]) && !empty($conf[3]))
						{
							throw new \InvalidArgumentException('La valeur de verification pour le champ password specifie a '.$field.' doit etre de type bool');
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
							
							$field_champ = '<input type="password" name="'.$prefix_field.$field.'_verification" value="" />' ; 
							
							isset($conf[2]['verification']) ? $msg_error = $conf[2]['verification'] : $msg_error = 'La vérification n\'est pas bonne' ;
							
							$field_erreur = '<p class="msg_erreur">'.$msg_error.'</p>';
							
							if (count($this->erreurs) > 0 && in_array($value, $this->erreurs)) 
							{
								$formulaire .= $field_erreur ;
							}
							
							$formulaire .= $field_label.$field_champ;
						}
					}
				}
				elseif($conf[0] == 'hidden' && $conf[1] === true)
				{
					if(isset($entity[$field]))
					{
						$hidden = '<input type="hidden" name="'.$prefix_field.$field.'" value="'.$entity[$field].'"/>' ;	
						$formulaire .= $hidden ;
					}
					else
					{
						throw new \InvalidArgumentException('Le champ hidden '.$field.' n\'est pas un attribut ou n\'est pas attribue a l\'objet : '.$this->entity);
					}
				}
                elseif($conf[0] == 'submit')
				{
                    !empty($conf[1]) ? $value = $conf[1] : $value = 'enregistrer' ;
                    
                    $submit = '<input type="submit" value="'.$value.'" name="'.$field.'"' ;
                    
                    if(isset($conf[2]) && !empty($conf[2]))
                    {
                        $submit .= ' class="'.$conf[2].'"' ;
                    }
					
					if(isset($conf[3]) && !empty($conf[3]))
                    {
                        $submit .= ' id="'.$conf[3].'"' ;
                    }
                    
                    $submit .= '/>' ;

                    $formulaire .= $submit ;           
                }
				elseif($conf[0] != 'form')
				{
					$champ = '<input type="'.$conf[0].'" name="'.$prefix_field.$field.'" value="'.$conf[1].'"/>' ;
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
			elseif($conf[0] == 'multiform')
			{
				$multiform = $this->multiform->form(false) ;
			}
		}
        
		$formulaire .= $multiform ;
       
        $this->var_submit[0] == 'Enregistrer_'.$this->entity ? $formulaire .= '<input type="submit" value="Enregistrer" name="Enregistrer_'.$this->entity.'" />' : true ;
        
		$formulaire .= $token.'</form>';
        
		return $formulaire ;
	}
	
	public function processForm(\Library\HTTPRequest $request, $validate = false, array $values = array(), $token = true)
	{
        if(is_object($this->objet))
        {
            if(!is_bool($validate) && $validate != 1)
            {
                throw new \InvalidArgumentException('La valeur de verifiaction de validation $validate doit etre de type bool ou egale a 1');	
            }
            
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
            
            $url .= $request->requestURI() ;

            $id = $this->app->user()->key() ;
            
            $save_select = array () ;
            $erreurs = array () ;
            $tab_liste = array () ;
            $multiform = true ;
            
            if(!$this->app->user()->isValidToken(1200, $url, $this->entity.'_'.$id) && $token === true)
            {
                $this->app->user()->setFlash('Formulaire expiré');
                $this->app->httpResponse()->redirect($request->requestURI());
            }
            
            $array_entity = array () ;
            
            if(count($values) > 0)
            {
                foreach($values as $value => $val)
                {
                    if(!in_array($value, $this->vars))
                    {
                        throw new \InvalidArgumentException('La valeur de l\'attribut '.$value.' dans $values n\'est pas valide');	
                    }
                    else
                    {
                        //$array_entity[$value] = $val ;
                        $method = 'set'.ucfirst($value) ;
                        $this->objet->$method($val) ;
                    }
                }
            }
            
            foreach($this->vars as $attribut)
            {
                if($request->postExists($this->entity.'_'.$attribut))
                {
                    //$array_entity[$attribut] = $request->postData($this->entity.'_'.$attribut) ;
                    if(!is_array($request->postData($this->entity.'_'.$attribut)))
                    {
                        $method = 'set'.ucfirst($attribut) ;
                        $this->objet->$method($request->postData($this->entity.'_'.$attribut)) ;
                    }
                }
            }
            
            $entity = $this->objet ;
            
            $add_erreurs = array();

            $nb_erreur = 10000 ;
            
            $prefix_field = $this->entity.'_' ;
            
            foreach($this->fields as $field => $conf)
            {
                if($conf[0] != 'div' && $conf[0] != '/div' && $conf[0] != 'p' && $conf[0] != 'h')
                {
                    if(isset($conf[0]) && $conf[0] != 'multiform')
                    {
                        if($conf[0] == 'liste' || $conf[0] == 'multiple' || $conf[0] == 'radio' || $conf[0] == 'checkbox')
                        {
                            $nb_erreur ++ ;
                            
                            $keys_form = array_keys($conf[4]); 
                                    
                            if(is_object($conf[4][$keys_form[0]]))
                            {
                                if(count($conf[4][$keys_form[0]]) > 0)
                                {
                                    $RC = new \ReflectionClass($conf[4][$keys_form[0]]);
                                    $name = $RC->getName();
                                    
                                    $exp = explode("\\", $name);
                                    $name = $exp[count($exp)-1];
                                    
                                    $liste = $this->managers->getManagerOf($this->entity())->DEF->getAssociate($entity['id'], $name) ;
                                    
                                    $liste_entities = $this->managers->getManagerOf($name)->DEF->getList() ;
                                    
                                    foreach($liste as $entity_liste)
                                    {
                                        if(isset($entity_liste[$name]))
                                        {
                                            $tab_liste[] = $entity_liste[$name] ;
                                        }
                                    }
                                }
                                else
                                {
                                    throw new \InvalidArgumentException('Le Tableaux d\'options de la liste specifie a '.$field.' ne doit pas être vide');	
                                }
                                
                                if($conf[0] == 'multiple' || $conf[0] == 'checkbox')
                                {
                                    if(isset($conf[6]) && !empty($conf[6]))
                                    {
                                        $select = $conf[6] ;
                                    }
                                    else
                                    {
                                        $select = 1 ;
                                    }
                                    
                                    if(count($request->postData($prefix_field.$field)) < $select)
                                    {
                                        $add_erreurs[] = $nb_erreur ;
                                    }
                                    else
                                    {
                                        $save = array() ;
                                        
                                        foreach($request->postData($prefix_field.$field) as $id_entity)
                                        {
                                            $id_entity = $this->crypt->decrypt($id_entity) ;
                                            
                                            if(!in_array($id_entity, $tab_liste))
                                            {
                                                $save[] = $id_entity ;
                                            }
                                        }
                                        
                                        foreach($liste_entities as $entity_liste)
                                        {
                                            if(!in_array($this->crypt->encrypt($entity_liste['id']), $request->postData($prefix_field.$field)))
                                            {
                                                method_exists($this->managers->getManagerOf($this->entity), 'deleteAssociate') ? $this->managers->getManagerOf($this->entity)->deleteAssociate($entity['id'], $entity_liste['id'], $name) : $this->managers->getManagerOf($this->entity)->DEF->deleteAssociate($entity['id'], $entity_liste['id'], $name) ;
                                            }
                                        }
                                        
                                        $save_select[] = array($save, $name) ;
                                    }
                                }
                                else
                                {
                                    $post = $request->postData($prefix_field.$field) ;
                                    
                                    if(!empty($post))
                                    {
                                        $conf[0] == 'liste' ? $id_entity = $this->crypt->decrypt($post[0]) : $id_entity = $this->crypt->decrypt($post) ;
                                        
                                        if(!in_array($id_entity, $tab_liste))
                                        {
                                            foreach($liste_entities as $entity_liste)
                                            {	
                                                if(in_array($entity_liste['id'], $tab_liste))
                                                {
                                                    method_exists($this->managers->getManagerOf($this->entity), 'deleteAssociate') ? $this->managers->getManagerOf($this->entity)->deleteAssociate($entity['id'], $entity_liste['id'], $name) : $this->managers->getManagerOf($this->entity)->DEF->deleteAssociate($entity['id'], $entity_liste['id'], $name) ;
                                                }
                                            }
                                            
                                            $save_select[] = array($id_entity, $name) ;
                                        }
                                    }
                                    else
                                    {
                                        $add_erreurs[] = $nb_erreur ;
                                    }
                                }
                            }
                            else
                            {
                                if($request->postExists($prefix_field.$field))
                                {
                                    $post = $request->postData($prefix_field.$field) ;
                                    
                                    if(!in_array($field, $this->adds_fields))
                                    {
                                        $method = 'set'.ucfirst($field) ;
                                        
                                        if($conf[0] == 'liste' )
                                        {
                                            $this->objet->$method($post[0]) ;
                                        }
                                        elseif($conf[0] == 'radio' )
                                        {
                                            $this->objet->$method($post) ;
                                        }
                                        elseif($conf[0] == 'multiple' || $conf[0] == 'checkbox')
                                        {
                                            $this->objet->$method($post) ;
                                        }
                                    }
                                    else
                                    {
                                        $this->objet->$field = $post ;	
                                    }
                                }
                                else
                                {
                                    $add_erreurs[] = $nb_erreur ;
                                }
                            }
                        }
                        elseif($conf[0] != 'hidden' && $conf[0] != 'submit' && $conf[0] != 'button' && $conf[0] != 'form' && $conf[0] != 'file')
                        {
                            if(!in_array($field, $this->adds_fields))
                            {
                                $const = strtoupper($field.'_INDISPONIBLE') ;
                                
                                if(defined($this->namespace."::$const"))
                                {
                                    if($this->managers->getManagerOf($this->entity)->DEF->count(array($field => $entity->$field()), array('id' => $entity->id())) != 0)
                                    {
                                        $add_erreurs[] = constant($this->namespace."::$const") ;
                                    }
                                }
                                
                                if($conf[0] == 'password' && $conf[3] === true)
                                {
                                    $const = strtoupper($field.'_VERIFICATION') ;
                                    
                                    if($request->postData($prefix_field.$field) != $request->postData($prefix_field.$field.'_verification'))
                                    {
                                        $add_erreurs[] = constant($this->spacename()."::$const") ;
                                    }
                                }
                            }
                            else
                            {
                                $value = $request->postData($prefix_field.$field) ;
                                
                                foreach($conf[2] as $error => $msg)
                                {
                                    $nb_erreur ++ ;
                                    
                                    if($error != 'int' && $error != 'string' && $error != 'empty')
                                    {		
                                        if(!preg_match('/^regex\(/', $error))
                                        {
                                            $method = 'is_'.ucfirst($error);
                                            
                                            if(!$validator->$method($value))
                                            {
                                                $add_erreurs[] = $nb_erreur ;
                                            }
                                        }
                                        else
                                        {
                                            $regex = substr($error, 6, -1);
        
                                            if (!preg_match($regex,$value)) 
                                            {
                                                $add_erreurs[] = $nb_erreur ;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if($error == 'string')
                                        {
                                            if(!is_string($value))
                                            {
                                                $add_erreurs[] = $nb_erreur ;
                                            }
                                        }
                                        elseif($error == 'int')
                                        {
                                            if(!ctype_digit($value) && !is_int($value))
                                            {
                                                $add_erreurs[] = $nb_erreur ;
                                            }
                                        }
                                        elseif(empty($value) || $value == '')
                                        {
                                            $add_erreurs[] = $nb_erreur ;
                                        }
                                    }
                                }
                            }
                        }
                        elseif($conf[0] == 'file')
                        {
                            if(isset($_FILES[$prefix_field.$field]) && $_FILES[$prefix_field.$field]['size'] != 0)
                            {
                                $dossier = $_SERVER["DOCUMENT_ROOT"].$conf[4];
                                
                                $method = 'set'.ucfirst($field) ;
                                
                                $taille = filesize($_FILES[$prefix_field.$field]['tmp_name']);
                                
                                $extension = explode('.', $_FILES[$prefix_field.$field]['name']);
                                $extension = strtolower($extension[count($extension)-1]);
                                
                                !empty($conf[3]) ? $name = $conf[3] : $name = basename($_FILES[$prefix_field.$field]['name']);
                                $name = strtr($name, 
                                      'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
                                      'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
                                $name = preg_replace('/([^.a-z0-9]+)/i', '', $name);
                                
                                $error = false ;
                                
                                $nb_erreur ++ ;
                                if($taille > $conf[5])
                                {
                                    $add_erreurs[] = $nb_erreur ;
                                    $error = true ;
                                }
                                
                                $nb_erreur ++ ;
                                if(!in_array($extension, $conf[6]))
                                {
                                    $add_erreurs[] = $nb_erreur ;
                                    $error = true ;
                                }
                                
                                $nb_erreur ++ ;
                                
                                if($error == false)
                                {
                                    if($this->objet->id() != null && $this->objet->$field() != null)
                                    {
                                        $path = str_replace(PATH, $_SERVER["DOCUMENT_ROOT"], $this->objet->$field()) ;
                                        
                                        if(is_file($path))
                                        {
                                            unlink($path) ? $error = false : $error = true ;
                                        }
                                        else
                                        {
                                            $this->objet->$method('') ;
                                        }
                                    }
                                    
                                    
                                    if($error == true || !move_uploaded_file($_FILES[$prefix_field.$field]['tmp_name'], $dossier . $name))
                                    {
                                        $add_erreurs[] = $nb_erreur ;
                                    }
                                    elseif($error == false)
                                    {
                                        $this->objet->$method(PATH.$conf[4].$name) ;
                                    }
                                }
                            }
                        }
                    }
                    elseif(isset($conf[0]))
                    {
                        $values = array($this->arg_multiform => rand()) ;
                        $multiform = $this->multiform->processMultiform(true, $values, false);
                    }
                }
            }
            
            $erreurs = array_merge($entity->erreurs(), $add_erreurs) ;
            
            if(count($erreurs) === 0 && ($multiform === true || is_array($multiform)))
            {
                if($validate === true)
                {	
                    return $entity ;
                }
                
                if($validate === false)
                {
                    method_exists($this->managers->getManagerOf($this->entity), 'save') ? $this->managers->getManagerOf($this->entity)->save($entity, $this->app->config()->getGlobal('encrypt_key')) : $this->managers->getManagerOf($this->entity)->DEF->save($entity, $this->app->config()->getGlobal('encrypt_key'));
                    
                    empty($entity['id']) ? $id_entity = $this->managers->getManagerOf($this->entity)->dao->lastInsertId() : $id_entity = $entity['id'] ;
                    
                    if(count($save_select) > 0)
                    {
                        foreach($save_select as $select)
                        {
                            if(is_array($select[0]))
                            {
                                foreach($select[0] as $select_id)
                                {
                                    method_exists($this->managers->getManagerOf($this->entity), 'associate') ? $this->managers->getManagerOf($this->entity)->associate($id_entity, $select_id, $select[1]) : $this->managers->getManagerOf($this->entity)->DEF->associate($id_entity, $select_id, $select[1]) ;
                                }
                            }
                            else
                            {
                                method_exists($this->managers->getManagerOf($this->entity), 'associate') ? $this->managers->getManagerOf($this->entity)->associate($id_entity, $select[0], $select[1]) : $this->managers->getManagerOf($this->entity)->DEF->associate($id_entity, $select[0], $select[1]) ;	
                            }
                        }
                    }
                    
                    if(count($this->adds_fields) > 0)
                    {
                        foreach($this->adds_fields as $field)
                        {
                            $conf = $this->fields[$field] ;
                            
                            if($conf[0] == 'textarea')
                            {
                                $objet = $conf[5] ;
                            }
                            elseif($conf[0] == 'radio')
                            {
                                $objet = $conf[6] ;
                            }
                            else
                            {
                                $objet = $conf[3] ;
                            }
                            
                            $RC = new \ReflectionClass($objet);
                            $name = $RC->getName();
                            $name = explode("\\", $name);
                            $name = $name[count($name)-1];
                            
                            $this->managers->getManagerOf($name)->DEF->updateArgAssociate($field, $request->postData($prefix_field.$field), $this->objet->id(), $this->entity, $objet->id()) ;
                            
                            /*method_exists($this->managers->getManagerOf($name), 'updateArgAssociate') ? 
                            $this->managers->getManagerOf($name)->updateArgAssociate($field, $request->postData($prefix_field.$field), $this->objet->id(), $this->entity, $objet->id()) : 
                            $this->managers->getManagerOf($name)->DEF->updateArgAssociate($field, $request->postData($prefix_field.$field), $this->objet->id(), $this->entity, $objet->id()) ;*/
                        }
                    }
                    
                    $this->valide == '' ? $flash = $this->entity.' sauvegardé' : $flash = $this->valide ;
                }
                else
                {
                    $flash = 'le formulaire ne comporte pas d\'erreurs' ;
                }
                
                if(is_array($multiform))
                {
                    isset($id_entity) ? $values = array($this->arg_multiform => $id_entity) : $values = array($this->arg_multiform => rand()) ;
                    $this->multiform = $this->multiform->processMultiform($validate, $values, false);
                }
            }
            else
            {
                if($multiform === false)
                {
                    $values = array($this->arg_multiform => rand()) ;
                    $this->multiform = $this->multiform->processMultiform(1, $values, false);
                }
                
                if($validate === true)
                {
                    return false ;	
                }
                
                $flash = 'Le formulaire comporte des erreurs' ;
            }
            
            $this->app->user()->setFlash($flash) ;
            $this->erreurs = $erreurs;
            
            $token =  $this->app->user()->generateToken($this->entity.'_'.$id) ;
            $this->token = $token ;
            
            return $this ;
        }
        else
        {
             throw new \InvalidArgumentException('Vous devez utiliser un objet pour utiliser ProcessForm');
        }
	}
	
	public function newToken()
	{
		$id = $this->app->user()->key() ;
		$this->token = $this->app->user()->generateToken($this->entity.'_'.$id) ;
	}
	
	public function entity()
	{
		return $this->entity;
	}
	
	public function objet()
	{
		return $this->objet;
	}
	
	public function vars()
	{
		return $this->vars;
	}
	
	public function bol()
	{
		return $this->bol ;
	}
	
	public function multiform()
	{
		return $this->multiform;
	}
	
	public function fields()
	{
		return $this->fields;
	}
	
	public function spacename()
	{
		return $this->namespace;
	}
	
	public function valide()
	{
		return $this->valide;
	}
	
	public function erreurs()
	{
		return $this->erreurs;
	}
	
	public function SetObjet($objet = '')
	{
		$this->objet = $objet ;
	}
}