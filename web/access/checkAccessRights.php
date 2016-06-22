<?php
include("access/db.php");

function readAccessFile($file)
{
  $handle = @fopen($file,"r");
  if ($handle) {
    $i = 0;
    $users = "";
    while (($buffer = fgets($handle, 4096)) !== false) {
      $buffer = chop($buffer);
      $buffer = strtolower($buffer);
      if ($buffer != "")  {
        $users[$i] = chop($buffer);
        $i = $i + 1;
      }
    }
    if (!feof($handle)) {
      print "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
  return $users;
}

function readAdminTable()
{
  $levels = "";
  $link = getLink();
  if ($link)
    $levels = readAdmins($link);

  return $levels;
}

function isMaster()
{
  // see whether the email is in the access list
  $email = strtolower(strtolower($_SERVER['SSL_CLIENT_S_DN_Email']));
  // open file with access list
  $levels = readAdminTable();
  $level = -1;
  if (array_key_exists($email,$levels))
    $level = $levels[$email];

  if ($level > 0)
    return true;

  return false;
}

function isAdmin()
{
  // see whether the email is in the access list
  $email = strtolower(strtolower($_SERVER['SSL_CLIENT_S_DN_Email']));

  // open file with access list
  $levels = readAdminTable();
  $level = -1;
  if (array_key_exists($email,$levels))
    $level = $levels[$email];

  if ($level > -1)
    return true;

  return false;
}

function isTa()
{
  // read TA table
  $link = getLink();
  $tas = readFullTaTable($link);

  // make sure table is not empty
  if ($tas == '') {
    //print ' ERROR -- TA table is empty';
    return false;
  }
  
  // see whether the email is in the access list
  $email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
  $find =  array_search($email,$tas);
  if ($tas[$find] == $email)
    return true;

  return false;
}

function isTeacher()
{
  // read Teacher table
  $link = getLink();
  $teachers = readTeacherTable($link);
  
  // see whether the email is in the access list
  $email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
  $find =  array_search($email,$teachers);
  if ($teachers[$find] == $email)
    return true;

  return false;
}

function allowed()
{
  if (isMaster() || isAdmin() || isTa() || isTeacher()) {
    return true;
  }
  return false;
}

function forbidden()
{
  if (! allowed()) {
    return true;
  }
  return false;
}

function exitParameterError($parameter)
{
  print "<p> ERROR the input parameter did not go through ($parameter). Please check!</p><hr>";
  print ' ';
  exit(); 
}

function exitAccessError()
{
  print '<p> ERROR you are not allowed to enter this page.<br>'; 
  print '    You are: ' . $_SERVER['SSL_CLIENT_S_DN_CN'] . ' with email: ' . 
    $_SERVER['SSL_CLIENT_S_DN_Email'] . '.</p><hr>'; 
  print ' ';
  exit(); 
}

?>
