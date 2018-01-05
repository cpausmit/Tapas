<?php
include_once("app/models/Utils.php");

class Dbc
{
  protected static $instance = null;
  protected function __construct()
  {
    // Thou shalt not construct that which is unconstructable!
  }
  protected function __clone()
  {
    // Me not like clones! Me smash clones!
  }
  public static function getWriter()
  {
    if (!isset(static::$instance)) {
      //$GLOBALS['DB_CREATIONS'] = $GLOBALS['DB_CREATIONS']+1;
      $pars = findAccessParameters();
      $host = $pars[0];
      $user = $pars[1];
      $passwd = $pars[2];
      static::$instance = new PDO('mysql:host='.$host.';dbname=Teaching;charset=utf8',$user,$passwd);
    }
    return static::$instance;
  }
  public static function getReader()
  {
    if (!isset(static::$instance)) {
      //$GLOBALS['DB_CREATIONS'] = $GLOBALS['DB_CREATIONS']+1;
      $pars = findAccessParameters();
      $host = $pars[0];
      $user = $pars[1];
      $passwd = $pars[2];
      static::$instance = new PDO('mysql:host='.$host.';dbname=Teaching;charset=utf8',$user,$passwd);
    }
    return static::$instance;
  }
}

?>
