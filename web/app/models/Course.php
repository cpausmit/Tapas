<?php

// +---------+----------+------+-----+---------+-------+
// | Field   | Type     | Null | Key | Default | Extra |
// +---------+----------+------+-----+---------+-------+
// | Number  | char(10) | YES  |     | NULL    |       | 
// | Name    | char(80) | YES  |     | NULL    |       | 
// | Version | int(11)  | YES  |     | NULL    |       | 
// +---------+----------+------+-----+---------+-------+

include_once("app/models/Dbc.php");

class Courses
{
  // Property declaration
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank course
    $instance = new self();
    return $instance;
  }

  public static function fromDb()
  {
    // 'constructor' returns full list of courses
    $instance = new self();
    $rows = Dbc::getReader()->query("select * from Courses order by Number");
    foreach ($rows as $key => $row)
      $instance->addCourse(Course::fromRow($row));
    
    return $instance;
  }

  public function addCourse($course)
  {
    if (!isset($this->list[$course->number]))
      $this->list[$course->number] = $course;
    else
      print " ERROR - trying to add a course resource twice.<br>\n";
    
    return;
  }
}

class Course
{
  // Property declaration
  public $number = '';
  public $name = '';
  public $version = -1;

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank course
    $instance = new self();
    return $instance;
  }

  public static function fromNumber($number) // 'constructor' with just number given
  {
    $instance = new self();
    $sql = "select * from Courses where Number = '".$number."'";
    $courses = Dbc::getReader()->query($sql);
    foreach ($courses as $key => $row) {
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
    $this->number = $row[0];
    $this->name = $row[1];
    $this->version = intval($row[2]);
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relvant infromation

    print "<tr>\n";
    print "<td>&nbsp;";
    print "<a href=\"showTaskSummary?number=".$this->number."\">" . $this->number . "</a>";
    print "&nbsp;</td>";
    print "<td>&nbsp;";
    print $this->name;
    print "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " Number";
    print "&nbsp;</th>";
    print "<th>&nbsp;";
    print " Description";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  public function isFresh()
  {
    // say whether this is a fresh record
    return ($this->number == '');
  }

  public function addToDb()
  {
    // adding the given course instance to the database

    // check for duplicate
    print '<br> Input is valid.Forming the SQL. <br>';
    $vals = sprintf("('%s','%s',%d)",$this->number,$this->name,$this->version);
    $sql = " insert into Courses values $vals";
    print "<br> SQL: $sql <br>";
    Dbc::getReader()->Exec($sql);
  }

  public function updateDb()
  {
    // updating the given course instance to the database

    print '<br> Forming the SQL. <br>';

    $form = "Number = '%s', Name = '%s', Version = %d";
    $vals = sprintf($form,$this->number,$this->name,$this->version);
    $sql = " update Courses set $vals where Number = '$this->number';";
    print "<br> SQL: $sql <br>";
    Dbc::getReader()->Exec($sql);
  }

  // Simple accessors
  public function getNumber() { return $this->number; }
  public function getName() { return $this->name; }
  public function getVersion() { return $this->version; }

}

?>
