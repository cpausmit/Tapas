<?php
include("app/models/Utils.php");

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
    $rows = $db->exec("select * from Students where Email = '$email'");
    $nRows = sizeof($rows);
    if ($nRows == 1)
      $instance->fill($rows[0]);
    else
      print " ERROR -- loading failed (nRows: $nRows)\n";
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

  public function addToDb($db)
  {
    // adding the given student instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      print '<br> Forming the SQL. <br>';
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

  // Simple accessors
  public function getFirstName() { return $this->firstName; }
  public function getLastName() { return $this->lastName; }
  public function getEmail() { return $this->email; }
  public function getAdvisorEmail() { return $this->advisorEmail; }
  public function getSupervisorEmail() { return $this->supervisorEmail; }
  public function getYear() { return $this->year; }
  public function getDivision() { return $this->division; }
  public function getResearch() { return $this->research; }

  // Property declaration
  public $firstName = '';
  public $lastName = '';
  public $email = 'EMPTY@mit.edu';
  public $advisorEmail = 'EMPTY@mit.edu';
  public $supervisorEmail = 'EMPTY@mit.edu';
  public $year = 0;
  public $division = '';
  public $research = '';                    

}

?>
