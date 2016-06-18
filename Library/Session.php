<?php
namespace Library;

class Session
{
    public $session_time = 3600;
    public $session = array();
    private $db;
    
    public function __construct($sql_host, $sql_user, $sql_password, $sql_db)
    {
        $this->host = $sql_host;
        $this->user = $sql_user;
        $this->password = $sql_password;
        $this->dba = $sql_db;
    }
    
    public function open ()//pour l'ouverture
    {
		$this->connect = mysqli_connect($this->host, $this->user, $this->password)or die('Erreur lors de la connexion au serveur') ;
		
		$bdd = mysqli_select_db($this->connect,$this->dba) ;

		$this->gc();	
		return $bdd;//true ou false selon la réussite ou non de la connexion à la bdd
    }
    
    public function read ($sid)//lecture
    {
        $sid = mysqli_real_escape_string($this->connect,$sid);
        $sql = "SELECT sess_datas FROM ".PREFIX_TABLE."session
                WHERE sess_id = '$sid' ";
        
        $query = mysqli_query($this->connect,$sql);   
        $data = mysqli_fetch_array($query);
        
        if(empty($data)) return FALSE;
        else return $data['sess_datas'];//on retourne la valeur de sess_datas
    }
    
    public function write ($sid, $data)//écriture
    {
        $expire = intval(time() + $this->session_time);//calcul de l'expiration de la session
        $data = mysqli_real_escape_string($this->connect,$data);//si on veut stocker du code sql 
		$ip = $_SERVER['REMOTE_ADDR'];
        
        $sql = "SELECT COUNT(sess_id) AS total
            FROM ".PREFIX_TABLE."session
            WHERE sess_id = '$sid' ";
        
        $query = mysqli_query($this->connect,$sql);
        $return = mysqli_fetch_array($query);
        if($return['total'] == 0)//si la session n'existe pas encore
        {
            $sql = "INSERT INTO ".PREFIX_TABLE."session
                VALUES('$sid','$data','$expire','$ip')";//alors on la crée
            
        }
        else//sinon
        {
			$sql = "SELECT sess_ip
            FROM ".PREFIX_TABLE."session
            WHERE sess_id = '$sid' ";
        
       		$query = mysqli_query($this->connect,$sql) or exit(mysqli_error($this->connect));
			
        	$return = mysqli_fetch_array($query);
			
			if($return['sess_ip'] == $ip)//si l'ip est identique
			{
				$sql = "UPDATE ".PREFIX_TABLE."session 
					SET sess_datas = '$data',
					sess_expire = '$expire'
					WHERE sess_id = '$sid' ";//on la modifie
			}
			else
			{
				$this->destroy(session_id());
			}
        }       
        $query = mysqli_query($this->connect,$sql) or exit(mysqli_error($this->connect));
        
        return $query;
    }
    
    public function close()//fermeture
    {
        mysqli_close($this->connect);//on ferme la bdd
    }
    
    public function destroy ($sid)//destruction
    {
        $sql = "DELETE FROM ".PREFIX_TABLE."session
            WHERE sess_id = '$sid' ";//on supprime la session de la bdd
        $query = mysqli_query($this->connect,$sql) or exit(mysqli_error($this->connect));
        return $query;
    }
    
    public function gc ()//nettoyage
    {
        $sql = "DELETE FROM ".PREFIX_TABLE."session
                WHERE sess_expire < ".time(); //on supprime les vieilles sessions 
                
        $query = mysqli_query($this->connect,$sql) or exit(mysqli_error($this->connect));
        
        return $query;
    }
    
}
