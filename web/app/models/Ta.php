<?php

//+----------+------------+------+-----+---------+-------+
//| Field    | Type       | Null | Key | Default | Extra |
//+----------+------------+------+-----+---------+-------+
//| Term     | char(5)    | YES  | MUL | NULL    |       |
//| Email    | char(40)   | YES  |     | NULL    |       |
//| FullTime | tinyint(4) | YES  |     | NULL    |       |
//| PartTime | tinyint(4) | YES  |     | NULL    |       |
//+----------+------------+------+-----+---------+-------+
// alter table Tas add constraint onePerTerm unique(Term, Email);

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

class Tas
{
  // Property declaration
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank ta
    $instance = new self();
    return $instance;
  }

  public static function fromDb($term)
  {
    // 'constructor' returns full list of tas
    $instance = new self();
    $taRows = Dbc::getReader()->query("select * from Tas where Term='$term' order by Email");
    foreach ($taRows as $key => $row)
      $instance->addTa(Ta::fromRow($row));
    
    return $instance;
  }

  public function addTa($ta)
  {
    if (!isset($this->list[$ta->email]))
      $this->list[$ta->email] = $ta;
    else
      print " ERROR - trying to add a ta twice.<br>\n";
    
    return;
  }

  public function printTable()
  {
    if (sizeof($this->list) != 0) {
      print "<table>\n";
      $first = true;
      foreach ($this->list as $key => $ta) {
        if ($first)
          $ta->printTableHeader(false);
        $ta->printTableRow(false);
        $first = false;
      }
      print "\n</table>\n";
    }
    else
      print " Ta list is empty.<br>\n";
    
    return;
  }
}

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

    $this->term = $row[0];
    $this->email = $row[1];
    $this->fullTime = $row[2];
    $this->partTime = $row[3];
  }

  protected function isValid()
  {
    // making sure TA information is valid

    $valid = true;
    
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

  public function printTableRow($open)
  {
    // print one row of a table with the relevant infromation

    print "<tr>\n";
    print "<td>&nbsp;" . $this->term . "&nbsp;</td>";
    print "<td>&nbsp;" . $this->email . "&nbsp;</td>";
    print "<td>&nbsp;" . $this->fullTime . "&nbsp;</td>";
    print "<td>&nbsp;" . $this->partTime . "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " Term";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " TA Email";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Fulltime";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Parttime";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }
    
  public function addToDb()
  {
    // adding the given ta instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      $vals = sprintf("('%s','%s',%d,%d)",
                      $this->term,$this->email,$this->fullTime,$this->partTime);
      $sql = " insert into Tas values $vals";
      Dbc::getReader()->Exec($sql);
    }
    else {
      print '<br> Invalid entry. STOP!<br>';
    }
  }
    
  // Property declaration
  public $term = '';
  public $email = 'EMPTY@mit.edu';
  public $fullTime = 0;
  public $partTime = 0;

}

?>
