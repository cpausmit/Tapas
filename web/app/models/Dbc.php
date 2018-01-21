<?php
include_once("app/models/AccessParameters.php");

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
      $p = AccessParameters::get();
      static::$instance = new PDO('mysql:host='.$p['host'].';dbname=Teaching;charset=utf8',
                                  $p['user'],$p['passwd']);
    }
    return static::$instance;
  }
  public static function getReader()
  {
    if (!isset(static::$instance)) {
      $p = AccessParameters::get();
      static::$instance = new PDO('mysql:host='.$p['host'].';dbname=Teaching;charset=utf8',
                                  $p['user'],$p['passwd']);
    }
    return static::$instance;
  }
}

?>
