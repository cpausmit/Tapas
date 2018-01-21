<?php

// mysql> describe Semesters;
// +---------+----------+------+-----+---------+-------+
// | Field   | Type     | Null | Key | Default | Extra |
// +---------+----------+------+-----+---------+-------+
// | Term    | char(5)  | YES  |     | NULL    |       | 
// +---------+----------+------+-----+---------+-------+

include_once("app/models/Dbc.php");

class Semesters
{
  // Property declaration
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank semesters
    $instance = new self();
    return $instance;
  }

  public static function fromDb()
  {
    // 'constructor' returns full list of courses
    $instance = new self();
    $semesterRows = Dbc::getReader()->query("select * from Semesters order by Term");
    foreach ($semesterRows as $key => $row) {
      $semester = Semester::fromRow($row);
      $instance->addSemester($semester);
    }
    
    return $instance;
  }

  public function addSemester($semester)
  {
    if (!isset($this->list[$semester->term]))
      $this->list[$semester->term] = $semester;
    else
      print " ERROR - trying to add a semester twice.<br>\n";
    
    return;
  }
}

class Semester
{
  // Property declaration
  public $term = '';

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank course
    $instance = new self();
    return $instance;
  }

  public static function fromTerm($term) // 'constructor' - term given
  {
    $instance = new self();
    $sql = "select * from Semesters where Term = '".$term."'";
    $courses = Dbc::getReader()->query($sql);
    foreach ($courses as $key => $row) {
      $instance->fill($row);
    }
    return $instance;
  }

  public static function fromRow(array $row)   // 'constructor' - db query row
  {
    $instance = new self();
    $instance->fill($row);
    return $instance;
  }

  protected function fill(array $row)
  {
    // here we fill the content
    $this->term = $row[0];
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relvant infromation

    print "<tr>\n";
    print "<td>&nbsp;";
    print "<a href=\"showTermSummary?number=".$this->term."\">" .
        $this->term . "</a>";
    print "&nbsp;</td>";
    print "<td>&nbsp;";
    print $this->term;
    print "&nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp;";
    print " TErm";
    print "&nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  public function isFresh()
  {
    // say whether this is a fresh record
    return ($this->term == '');
  }

  public function addToDb()
  {
    // adding the given semester instance to the database

    // check for duplicate
    //print '<br> Input is valid.Forming the SQL. <br>';
    $vals = sprintf("('%s')",$this->term);
    $sql = " insert into Semesters values $vals";
    //print "<br> SQL: $sql <br>";
    Dbc::getReader()->Exec($sql);
  }

  public function updateDb()
  {
    // updating the given course instance to the database

    //print '<br> Forming the SQL. <br>';
    $form = "Number = '%s', Name = '%s', Version = %d";
    $vals = sprintf($form,$this->term);
    $sql = " update Semesters set $vals where Term = '$this->term';";
    //print "<br> SQL: $sql <br>";
    Dbc::getReader()->Exec($sql);
  }

  // Simple accessors
  public function getTerm() { return $this->term; }

}

?>
