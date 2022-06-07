<?php

include_once("app/models/Admin.php");
include_once("app/models/Ta.php");
include_once("app/models/Tables.php");
include_once("app/models/Teacher.php");

function isMaster()
{
  // see whether the email is in the access list and what level is given

  // find ssl client email id
  //print '<p> ERROR you are not allowed to enter this page.<br>';
  //foreach ($_SERVER as $key_name => $key_value) {
  //  print $key_name . " = " . $key_value . "<br>";
  //}
  $email = strtolower(strtolower($_SERVER['SSL_CLIENT_S_DN_Email']));

  // initialize to no access
  $level = -1;

  // get admins from database
  $admins = Admins::fromDb();
  if (array_key_exists($email,$admins->list))
    $level = $admins->list[$email]->level;

  if ($level > 0)
    return true;

  return false;
}

function isAdmin()
{
  // see whether the email is in the access list
  $email = strtolower(strtolower($_SERVER['SSL_CLIENT_S_DN_Email']));

  // initialize to no access
  $level = -1;

  // get admins from database
  $admins = Admins::fromDb();
  if (array_key_exists($email,$admins->list))
    $level = $admins->list[$email]->level;
  
  if ($level > -1)
    return true;

  return false;
}

function isTa()
{
  // read TA table

  // get the active tables to consider
  $activeTables = new Tables('ActiveTables');
  $term = substr($activeTables->getUniqueMatchingName('Tas'),-5,5);
  $tas = Tas::fromDb($term);

  // out if the list is empty
  if (sizeof($tas->list) == 0)
    return false;

  // see whether the email is in the access list
  $email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
  if (array_key_exists($email,$tas->list))
    return true;

  return false;
}

function isTeacher()
{
  // read Teacher table
  $activeTables = new Tables('ActiveTables');
  $term = substr($activeTables->getUniqueMatchingName('Evaluations'),-5,5);
  $teachers = Teachers::fromAssignments($term);

  //$teachers->printTable();
  
  // see whether the email is in the access list
  $email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
  if (array_key_exists($email,$teachers->list))
    return true;

  return false;
}

function allowed()
{
  if (isMaster() || isAdmin() || isTa() || isTeacher())
    return true;

  return false;
}

function forbidden()
{
  if (! allowed())
    return true;

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
