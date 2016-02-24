<?php

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

class Student
{

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank student
    $instance = new self();
    return $instance;
  }

  public static function fromEmail($db,$email)
  {
    // 'constructor' with just email given
    $instance = new self();
    $sql = "select * from Students where Email = '$email'";
    //print " SQL: $sql";
    $students = $db->query($sql);
    foreach ($students as $key => $row) {
      $instance->fill($row);
    }
 
    //if ($instance->year == 0)
    //  print "<br> WARNING -- loading failed return empty (SQL: $sql)<br><br>\n";
 
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

    $this->firstName = $row[0];
    $this->lastName = $row[1];
    $this->email = $row[2];
    $this->advisorEmail = $row[3];
    $this->supervisorEmail = $row[4];
    $this->year = $row[5];
    $this->division = $row[6];
    $this->research = $row[7];
  }

  // Property declaration
  public $firstName = '';
  public $lastName = '';
  public $email = '';
  public $advisorEmail = '';
  public $supervisorEmail = '';
  public $year = 0;
  public $division = '';
  public $research = '';                    

  public function addToDb($db)
  {
    // adding the given student instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      // check for duplicate
      print '<br> Input is valid.Forming the SQL. <br>';
      $vals = sprintf("('%s','%s','%s','%s','%s',%d,'%s','%s')",$this->firstName,$this->lastName,
                      $this->email,$this->advisorEmail,$this->supervisorEmail,
                      $this->year,$this->division,$this->research);
      $sql = " insert into Students values $vals";
      print "<br> SQL: $sql <br>";
      $db->Exec($sql);
    }
    else {
      print '<br> Invalid entry. STOP!<br>';
    }
  }

  public function updateDb($db)
  {
    // adding the given student instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {

        // check for duplicate
      print '<br> Forming the SQL. <br>';

      $form = "FirstName = '%s', LastName = '%s'";
      $form = "$form , AdvisorEmail = '%s', SupervisorEmail = '%s', Year = %d";
      $form = "$form , Division = '%s', Research = '%s'";
      $vals = sprintf($form,$this->firstName,$this->lastName,
                      $this->advisorEmail,$this->supervisorEmail,
                      $this->year,$this->division,$this->research);
      $sql = " update Students set $vals where Email = '$this->email';";
      print "<br> SQL: $sql <br>";
      $db->Exec($sql);
    }
    else {
      print '<br> Invalid entry. STOP!<br>';
    }
  }
    
  protected function isValid()
  {
    // making sure student information is valid

    $valid = true;

    print '<br> validating student information ....<br>';

    if (isName($this->firstName))
      print " -- First Name valid.<br>\n";
    else
      return false;

    if (isName($this->lastName))
      print " -- Last Name valid.<br>\n";
    else
      return false;

    if (isEmail($this->email))
      print " -- Email valid.<br>\n";
    else
      return false;

    if (isEmail($this->advisorEmail))
      print " -- Advisor Email valid.<br>\n";
    else
      return false;
    
    if (isEmail($this->supervisorEmail))
      print " -- Supervisor Email valid.<br>\n";
    else
      return false;

    if ($this->year > 1970 and $this->year < 2020)
      print "Number is a year: $this->year -- Year valid.<br>\n";
    else {
      print "Number is not a year: $this->year.<br>\n";
      return false;
    }
   
    return $valid;
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relvant infromation

    print "<tr>\n";
    print "<td>&nbsp;";
    print $this->lastName . ', ' . $this->firstName;
    print "&nbsp;</td>";
    print "<td>&nbsp;";
    print "<a href=\"showTaSummary?email=" . $this->email . "\">" . $this->email . "</a>";
    print "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " Students Name";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Email";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  public function printSummary()
  {
    // print one row of a table with the relvant infromation

    print "<u><b>". $this->lastName . ', ' . $this->firstName . " (" . $this->email .
          ")</b></u>\n<br>";
    print ' joined: '. $this->year .
          ', division: ' . $this->division . ',   research: ' . $this->research . "\n<br>" .
          ' academic advisor: ' . $this->advisorEmail .
          ',   research supervisor: ' . $this->supervisorEmail . "\n";
  }

  public function isFresh()
  {
    // print one row of a table with the relvant infromation
    return ($this->year == 0);
  }

}

?>
