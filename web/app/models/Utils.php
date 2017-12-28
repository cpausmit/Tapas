<?php

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

function isEmail($email)
{
  $result = false;
  if (preg_match("/^[a-zA-Z0-9\_\-@.]+$/", $email) && preg_match("/@/i", $email)) {
    $result = true;
    print ''; // print " String is an email: $email" ;
  }
  else {
    print " String is a not an email: $email" ;
  }
  return $result;
}

function makeEmail($email)
{
  // Make sure that the email makes sense (add '@mit.edu' if not provided)
  if (!isEmail($email))
    $email = $email . '@mit.edu';
  return $email;
}

function getPostVariable($variableName)
{
  // read complete courses table
  $variable = 'undefined';
  if (array_key_exists($variableName,$_POST))
    $variable = $_POST[$variableName];
  else
    $GLOBALS['COMPLETE'] = 0;

  return $variable;
}

?>