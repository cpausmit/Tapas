<?php

class AccessParameters
{
  protected static $parameters = null;
  protected function __construct()
  {
    // Thou shalt not construct that which is unconstructable!
  }
  protected function __clone()
  {
    // HULK : Me not like clones! Me smash clones!
  }
  public static function get()
  {
    if (!isset(static::$parameters)) {
      $on = 0;
      $host = "";
      $user = "";
      $passwd = "";
      
      $handle = fopen('/etc/myTapas.cnf','r');
      while(!feof($handle)) {
        $tmp = fgets($handle);
        $tmp = substr_replace($tmp,"",-1);
        if ($tmp == "[mysql-tapas]") {
          $on = 1;
        }
        if ($on == 1 && ($host == "" || $user == "" || $passwd == "")) {
          if (strpos($tmp,'host=') !== false) {
            $a = explode('=',$tmp);
            $host = $a[1];
          }
          if (strpos($tmp,'user=') !== false) {
            $a = explode('=',$tmp);
            $user = $a[1];
          }
          if (strpos($tmp,'password=') !== false) {
            $a = explode('=',$tmp);
            $passwd = $a[1];
          }
        }
      }
      $status = fclose($handle);
      
      // add the parameters to the array
      static::$parameters = array();

      static::$parameters['host'] = $host;
      static::$parameters['user'] = $user;
      static::$parameters['passwd'] = $passwd;
    }

    #print "HOST: ". $host;
    #print "USER: ". $user;
    #print "PW: ". $passwd;
    return static::$parameters;
  }
}

?>
