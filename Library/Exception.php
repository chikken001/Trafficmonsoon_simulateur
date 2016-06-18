<?php
class MonException extends ErrorException
{
  public function __toString()
  {
    switch ($this->severity)
    {
      case E_USER_ERROR :
        $type = 'Erreur fatale';
        break;
      
      case E_WARNING : 
      case E_USER_WARNING : 
        $type = 'Attention';
        break;
      
      case E_NOTICE : 
      case E_USER_NOTICE : 
        $type = 'Note';
        break;
      
      default : 
        $type = 'Erreur inconnue';
        break;
    }
    
    return '<strong>' . $type . '</strong> : [' . $this->code . '] ' . $this->message . '<br /><strong>' . $this->file . '</strong> � la ligne <strong>' . $this->line . '</strong>';
  }
}
function error2exception($code, $message, $fichier, $ligne)
{
  // Le code fait office de s�v�rit�.
  // Reportez-vous aux constantes pr�d�finies pour en savoir plus.
  // http://fr2.php.net/manual/fr/errorfunc.constants.php
  throw new MonException($message, 0, $code, $fichier, $ligne);
}
function customException($e)
{
  echo 'Ligne ', $e->getLine(), ' dans ', $e->getFile(), '<br /><strong>Erreur</strong> : ', $e->getMessage();
}
set_error_handler('error2exception');
set_exception_handler('customException');