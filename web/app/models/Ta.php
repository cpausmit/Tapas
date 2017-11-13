<?php

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

class Ta
{

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' of fresh instance
    $instance = new self();
    return $instance;
  }

  public static function fromRow(array $row)
  {
    // 'constructor' using db query row
    $instance = new self();
    $instance->fill($row);
    return $instance;
  }

  protected function fill(array $row)
  {
    // here we fill the content

    $this->email = $row[0];
    $this->fullTime = $row[1];
    $this->partTime = $row[2];
  }

  protected function isValid()
  {
    // making sure TA information is valid

    $valid = true;

    //print '<br> validating TA information ....<br>';
    
    if (isEmail($this->email))
      print ""; //print " -- Email valid.<br>\n";
    else
      return false;
    
    if ($this->fullTime >= 0 and $this->fullTime <= 1)
      print ""; // print "Fulltime fraction: $this->fullTime -- valid.<br>\n";
    else {
      print "Fulltime fraction: $this->fullTime -- invalid.<br>\n";
      return false;
    }
    
    if ($this->partTime >= 0 and $this->partTime <= 1)
      print ""; // print "Parttime fraction: $this->partTime -- valid.<br>\n";
    else {
      print "Parttime fraction: $this->partTime -- invalid.<br>\n";
      return false;
    }

    return $valid;
  }
    
  public function addToDb($db,$table)
  {
    // adding the given student instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      //print '<br> Input is valid.Forming the SQL. <br>';
      $vals = sprintf("('%s',%d,%d)",$this->email,$this->fullTime,$this->partTime);
      $sql = " insert into $table values $vals";
      //print "<br> SQL: $sql <br>";
      $db->Exec($sql);
    }
    else {
      print '<br> Invalid entry. STOP!<br>';
    }
  }
    
  // Property declaration
  public $email = 'EMPTY@mit.edu';
  public $fullTime = 0;
  public $partTime = 0;

}

?>
