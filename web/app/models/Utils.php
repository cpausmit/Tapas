<?php

function findAccessParameters()
{
  //$GLOBALS['DB_CREATIONS'] = $GLOBALS['DB_CREATIONS']+1;
  $pars = "";
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
  $pars = array($host,$user,$passwd);

  return $pars;
}

function isName($name)
{
  $result = false;
  if (preg_match("/^[a-zA-Z\-]+$/", $name)) {
    $result = true;
    //print " String is a name: $name" ;
  }
  else {
    print " String is a not a name: $name" ;
  }
  return $result;
}

function isEmail($email,$quiet=false)
{
  $result = false;
  if (preg_match("/^[a-zA-Z0-9\_\-@.]+$/", $email) && preg_match("/@/i", $email)) {
    $result = true;
    print ''; // print " String is an email: $email" ;
  }
  else {
    if (!$quiet)
      print " String is a not an email: $email" ;
  }
  return $result;
}

function makeEmail($email)
{
  // Make sure that the email makes sense (add '@mit.edu' if not provided)
  if (!isEmail($email,true))
    $email = $email . '@mit.edu';
  return $email;
}

function getTables($db,$pattern)
{
  // find all tables that match a given pattern (WARNING - there cannot be a printout here)

  $tables = array(); // start with an empty array
  
  // query and go through the results
  $query = "show tables like '".$pattern."'";
  $tableRows = $db->query($query);
  foreach ($tableRows as $key => $row) {
    $tables[] = $row[0];
  }

  return $tables;
}  

?>
