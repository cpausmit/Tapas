<?php

// mysql> describe Teachers;
// +-----------+----------+------+-----+---------+-------+
// | Field     | Type     | Null | Key | Default | Extra |
// +-----------+----------+------+-----+---------+-------+
// | FirstName | char(20) | YES  |     | NULL    |       | 
// | LastName  | char(20) | YES  |     | NULL    |       | 
// | Email     | char(40) | YES  | UNI | NULL    |       | 
// | Position  | char(20) | YES  |     | NULL    |       | 
// | Status    | char(20) | YES  |     | NULL    |       | 
// +-----------+----------+------+-----+---------+-------+

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

class Teachers
{
  // Property declaration
  public $list = '';

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank teacher
    $instance = new self();
    return $instance;
  }

  public static function fromDb($db)
  {
    // 'constructor' returns full list of teachers
    $instance = new self();
    $teacherRows = $db->query("select * from Teachers order by Email");
    foreach ($teacherRows as $key => $row) {
      $teacher = Teacher::fromRow($row);
      $instance->addTeacher($teacher);
    }
    
    return $instance;
  }

  public function addTeacher($teacher)
  {
    if (!isset($this->list[$teacher->email]))
      $this->list[$teacher->email] = $teacher;
    else
      print " ERROR - trying to add a teacher twice.<br>\n";
    
    return;
  }
}

class Teacher
{

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank teacher
    $instance = new self();
    return $instance;
  }

  public static function fromEmail($db,$email) // 'constructor' with just email given
  {
    $instance = new self();
    $sql = "select * from Teachers where Email = '$email'";
    $teachers = $db->query($sql);
    foreach ($teachers as $key => $row) {
      $instance->fill($row);
    }
    return $instance;
  }

  public static function fromRow(array $row)   // 'constructor' using db query row
  {
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
    $this->position = $row[3];
    $this->status = $row[4];
  }

  public function isFresh()
  {
    // print one row of a table with the relevant infromation
    return ($this->firstName == '');
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relevant infromation

    print "<tr>\n";
    print "<td>&nbsp;";
    print $this->lastName . ', ' . $this->firstName;
    print "&nbsp;</td>";
    print "<td>&nbsp;";
    print "<a href=\"showTeacherSummary?email=" . $this->email . "\">" . $this->email . "</a>";
    print "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " Teachers Name";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Email";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  public function printSummary()
  {
    // print one row of a table with the relevant infromation

    print "<u><b>". $this->lastName . ', ' . $this->firstName . " (" . $this->email .
          ")</b></u>\n<br>";
    print ' position: ' . $this->position . ',   status: ' . $this->status . "\n<br>";
  }

public function addToDb($db)
  {
    // adding the given teacher instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      // check for duplicate
      print '<br> Input is valid.Forming the SQL. <br>';
      $vals = sprintf("('%s','%s','%s','%s','%s')",$this->firstName,$this->lastName,
                      $this->email,$this->position,$this->status);
      $sql = " insert into Teachers values $vals";
      print "<br> SQL: $sql <br>";
      $db->Exec($sql);
    }
    else {
      print '<br> Invalid entry. STOP!<br>';
    }
  }

  public function updateDb($db)
  {
    // adding the given teacher instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {

      // check for duplicate
      print '<br> Forming the SQL. <br>';

      $form = "FirstName = '%s', LastName = '%s'";
      $form = "$form , Position = '%s', Status = '%s'";
      $vals = sprintf($form,$this->firstName,$this->lastName,
                      $this->position,$this->status);
      $sql = " update Teachers set $vals where Email = '$this->email';";
      print "<br> SQL: $sql <br>";
      $db->Exec($sql);
    }
    else {
      print '<br> Invalid entry. STOP!<br>';
    }
  }

  protected function isValid()
  {
    // making sure teacher information is valid

    $valid = true;

    print '<br> validating teacher information ....<br>';

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
   
    return $valid;
  }

  // Simple accessors
  public function getFirstName() { return $this->firstName; }
  public function getLastName() { return $this->lastName; }
  public function getEmail() { return $this->email; }
  public function getPosition() { return $this->position; }
  public function getStatus() { return $this->status; }

  // Property declaration
  public $firstName = '';
  public $lastName = '';
  public $email = 'EMPTY@mit.edu';
  public $position = '';
  public $status = '';
}

?>
