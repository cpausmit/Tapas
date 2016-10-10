<?php

include_once("access/db.php");

class Dbc
{
  public static function getWriter() {
    $pars = findAccessParameters();
    $host = $pars[0];
    $user = $pars[1];
    $passwd = $pars[2];
    //echo " HOST: $host  USER $user  PASS: $passwd";
    
    static $db = null;
    if ($db === null)
      $db = new PDO('mysql:host='.$host.';dbname=Teaching;charset=utf8',$user,$passwd);
    return $db;
  }

  public static function getReader() {
    $pars = findAccessParameters();
    $host = $pars[0];
    $user = $pars[1];
    $passwd = $pars[2];
    //echo " HOST: $host  USER: $user  PASS: $passwd";
    
    static $db = null;
    if ($db === null)
      $db = new PDO('mysql:host='.$host.';dbname=Teaching;charset=utf8',$user,$passwd);
    return $db;
  }
}

?>
