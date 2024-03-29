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
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank teacher
    $instance = new self();
    return $instance;
  }

  public static function fromAssignments($term)
  {
    // 'constructor' returns list of teachers assigned during term for lecture
    $instance = new self();
    $sql = "select * from Teachers as t inner join Assignments as a on t.Email = a.Person"
        .  " where a.Term = '$term' and a.Task like '%-Lec-%' order by t.LastName";
    //print " SQL : $sql";
    $rows = Dbc::getReader()->query($sql);
    foreach ($rows as $key => $row)
      $instance->addTeacher(Teacher::fromRow($row),0);

    return $instance;
  }

  public static function fromDb()
  {
    // 'constructor' returns full list of teachers
    $instance = new self();
    $teacherRows = Dbc::getReader()->query("select * from Teachers order by Email");
    foreach ($teacherRows as $key => $row)
      $instance->addTeacher(Teacher::fromRow($row),1);
    
    return $instance;
  }

  public function addTeacher($teacher,$unique = 0)
  {
    //print "Adding: $teacher->email";
    if (!isset($this->list[$teacher->email]))
      $this->list[$teacher->email] = $teacher;
    else
      if ($unique != 0)
        print " ERROR - trying to add a teacher twice ($teacher->email).<br>\n";
    
    return;
  }

  public function printTable()
  {
    if (sizeof($this->list) != 0) {
      print "<table>\n";
      $first = true;
      foreach ($this->list as $key => $teacher) {
        if ($first)
          $teacher->printTableHeader(false);
        $teacher->printTableRow(false);
        $first = false;
      }
      print "\n</table>\n";
    }
    else
      print " Teacher list is empty.<br>\n";
    
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

  public static function fromEmail($email) // 'constructor' with just email given
  {
    $instance = new self();
    $sql = "select * from Teachers where Email = '$email'";
    $teachers = Dbc::getReader()->query($sql);
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

public function addToDb()
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
      Dbc::getReader()->Exec($sql);
    }
    else {
      print '<br> Invalid entry. STOP!<br>';
    }
  }

  public function updateDb()
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
      Dbc::getReader()->Exec($sql);
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
