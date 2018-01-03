<?php

//+-----------------+----------+------+-----+---------+-------+
//| Field           | Type     | Null | Key | Default | Extra |
//+-----------------+----------+------+-----+---------+-------+
//| FirstName       | char(40) | YES  |     | NULL    |       |
//| LastName        | char(40) | YES  |     | NULL    |       |
//| Email           | char(40) | YES  | UNI | NULL    |       |
//| AdvisorEmail    | char(40) | YES  |     | NULL    |       |
//| SupervisorEmail | char(40) | YES  |     | NULL    |       |
//| Year            | int(11)  | YES  |     | NULL    |       |
//| Division        | char(4)  | YES  |     | NULL    |       |
//| Research        | char(6)  | YES  |     | NULL    |       |
//+-----------------+----------+------+-----+---------+-------+

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

class Students
{
  // Property declaration
  public $list = '';

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank student
    $instance = new self();
    return $instance;
  }

  public static function fromDb($db)
  {
    // 'constructor' returns full list of students
    $instance = new self();
    $studentRows = $db->query("select * from Students order by Email");
    foreach ($studentRows as $key => $row)
      $instance->addStudent(Student::fromRow($row));
    
    return $instance;
  }

  public function addStudent($student)
  {
    if (!isset($this->list[$student->email]))
      $this->list[$student->email] = $student;
    else
      print " ERROR - trying to add a student twice.<br>\n";
    
    return;
  }
}

class Student
{
  // Property declaration
  public $firstName = '';
  public $lastName = '';
  public $email = '';
  public $advisorEmail = '';
  public $supervisorEmail = '';
  public $year = 0;
  public $division = '';
  public $research = '';                    

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
 
    if ($instance->year == 0)
      $instance->email = $email;
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

  public function addToDb($db)
  {
    // adding the given student instance to the database

    // make sure this is a valid new entry
    if ($this->isValid()) {
      // check for duplicate
      //print '<br> Input is valid.Forming the SQL. <br>';
      $vals = sprintf("('%s','%s','%s','%s','%s',%d,'%s','%s')",$this->firstName,$this->lastName,
                      $this->email,$this->advisorEmail,$this->supervisorEmail,
                      $this->year,$this->division,$this->research);
      $sql = " insert into Students values $vals";
      //print "<br> SQL: $sql <br>";
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
      //print '<br> Forming the SQL. <br>';

      $form = "FirstName = '%s', LastName = '%s'";
      $form = "$form , AdvisorEmail = '%s', SupervisorEmail = '%s', Year = %d";
      $form = "$form , Division = '%s', Research = '%s'";
      $vals = sprintf($form,$this->firstName,$this->lastName,
                      $this->advisorEmail,$this->supervisorEmail,
                      $this->year,$this->division,$this->research);
      $sql = " update Students set $vals where Email = '$this->email';";
      //print "<br> SQL: $sql <br>";
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

    //print '<br> validating student information ....<br>';

    if (isName($this->firstName))
      print "";
    //print " -- First Name valid.<br>\n";
    else
      return false;

    if (isName($this->lastName))
      print "";
    //print " -- Last Name valid.<br>\n";
    else
      return false;

    if (isEmail($this->email))
      print "";
    //print " -- Email valid.<br>\n";
    else
      return false;

    if (isEmail($this->advisorEmail))
      print "";
    //print " -- Advisor Email valid.<br>\n";
    else
      return false;
    
    if (isEmail($this->supervisorEmail) or $this->supervisorEmail == "?")
      print "";
    //print " -- Supervisor Email valid.<br>\n";
    else
      return false;

    if ($this->year > 1970 and $this->year < 2020)
      print "";
    //print "Number is a year: $this->year -- Year valid.<br>\n";
    else {
      print "Number is not a year: $this->year.<br>\n";
      return false;
    }
   
    return $valid;
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relevant infromation

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
    // print one row of a table with the relevant infromation

    print "<u><b>". $this->lastName . ', ' . $this->firstName . " (" . $this->email .
          ")</b></u>\n<br>";
    print ' joined: '. $this->year .
          ', division: ' . $this->division . ',   research: ' . $this->research . "\n<br>" .
          ' academic advisor: ' . $this->advisorEmail .
          ',   research supervisor: ' . $this->supervisorEmail . "\n";
  }

  public function isFresh()
  {
    // print one row of a table with the relevant infromation
    return ($this->year == 0);
  }

  public function printStudentForm($action)
  {
    print '<table>';
    print '<form  action="'.$action.'" method="post">'."\n";
    print '<tr><td>';
    print ' <b>FIXED EMAIL</b> ';
    print '</td><td>';
    print '<select class="email" name="email">'."\n";
    print '<option value="'.$this->email.'">'.$this->email.'</option>'."\n";
    print '</select>'."\n";
    print '</td></tr>';
    print '<tr><td> ------ </td><td> </td></tr>';
    print '<tr><td>';
    print '  First Name:&nbsp;'."\n";
    print '</td><td>';
    print '  <input type="text" name="firstName" value="'.$this->firstName.'"><br>'."\n";
    print '</td></tr>';
    print '<tr><td>';
    print '  Last Name:&nbsp;'."\n";
    print '</td><td>';
    print '  <input type="text" name="lastName" value="'.$this->lastName.'"><br>'."\n";
    print '</td></tr>';
    print '<tr><td>';
    print '  Academic Advisor email:&nbsp;'."\n";
    print '</td><td>';
    print '  <input type="text" name="advisorEmail" value="'.$this->advisorEmail.'"><br>'."\n";
    print '</td></tr>';
    print '<tr><td>';
    print '  Supervisor email:&nbsp;'."\n";
    print '</td><td>';
    print '  <input type="text" name="supervisorEmail" value="'.$this->supervisorEmail.'"><br>'."\n";
    print '</td></tr>';
    print '<tr><td>';
    print '  Year joined:&nbsp;'."\n";
    print '</td><td>';
    print '  <input type="text" name="year" value="'.$this->year.'"><br>'."\n";
    print '</td></tr>';
    print '<tr><td>';
    print '  Division:&nbsp;'."\n";
    print '</td><td>';
    print '  <input type="text" name="division" value="'.$this->division.'"><br>'."\n";
    print '</td></tr>';
    print '<tr><td>';
    print '  Research:&nbsp;'."\n";
    print '</td><td>';
    print '  <input type="text" name="research" value="'.$this->research.'"><br>'."\n";
    print '</td></tr>';
    print '<tr><td> ------ </td><td> </td></tr>';
    print '<tr><td></td><td>';
    // make sure to specify the type of action
    if ($this->isFresh())
      print '<input type="submit" value="submit new student record" />'."\n";
    else
      print '<input type="submit" value="submit updated student record" />'."\n";
    
    print '</td></tr>';
    print '</table>';
    print '</form>'."\n";
  }
}

?>
