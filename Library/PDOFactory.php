<?php
namespace Library;

class PDOFactory
{
  public static function getMysqlConnexion($dbname, $dblogin, $dbpassword, $host)
  {
	$db = new \PDO('mysql:host='.$host.';dbname='.$dbname, $dblogin, $dbpassword);
	$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	$db->query("SET NAMES 'utf8'");
    
    return $db;
  }
}