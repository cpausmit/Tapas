<?php

function isName($name)
{
  $result = false;
  if (preg_match("/^[a-zA-Z\-]+$/", $name)) {
    $result = true;
    print " String is a name: $name" ;
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
    print " String is an email: $email" ;
  }
  else {
    print " String is a not an email: $email" ;
  }
  return $result;
}

?>