<?php

// mysql> describe Assignments;
//+--------+------------+------+-----+---------+-------+
//| Field  | Type       | Null | Key | Default | Extra |
//+--------+------------+------+-----+---------+-------+
//| Term   | char(5)    | YES  |     | NULL    |       |
//| Task   | char(40)   | YES  | UNI | NULL    |       |
//| Person | char(40)   | YES  |     | NULL    |       |
//| EvalO  | tinyint(4) | YES  |     | -1      |       |
//+--------+------------+------+-----+---------+-------+

include_once("app/models/Dbc.php");
include_once("app/models/TeachingTask.php");

class Assignments
{
  // Property declaration
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank assignment
    $instance = new self();
    return $instance;
  }

  public static function fromDb($term)
  {
    // 'constructor' returns full list of assignments
    $instance = new self();

    if ($term == 'ALL')
      $rows = Dbc::getReader()->query("select * from Assignments");
    else
      $rows = Dbc::getReader()->query("select * from Assignments where Term='$term'");

    foreach ($rows as $key => $row)
      $instance->add(Assignment::fromRow($row));
    
    return $instance;
  }

  public function add($assignment)
  {
    $key = $assignment->task;
    if (!isset($this->list[$key]))
      $this->list[$key] = $assignment;
    else
      print " ERROR - trying to add an assignment twice.<br>\n";
    
    return;
  }

  public function show($option)
  {
    print "<table>\n";
    print "<tr><th>&nbsp; Term &nbsp;</th><th>&nbsp; Course &nbsp;</th><th> Type &nbsp;</th><th> Effort &nbsp;</th>";
    print "<th> TA type &nbsp;</th><th> Person &nbsp;</th><th> Id &nbsp;</th><th> EvalO &nbsp;</th></tr>\n";
    $iF = 0;
    $iP = 0;
    foreach ($this->list as $task => $assignment) {
      $myTask = new TeachingTask($task);
      $p = $assignment->person;
      $display = 0;

      if ($option == "ALL")
        $display = 1;
      else if ($option == "TA" && $myTask->isTa() && $p != "" && $p != "EMPTY@mit.edu")
        $display = 1;
      else if ($option == "Unassigned" && ($p == "" || $p == "EMPTY@mit.edu"))
        $display = 1;
      
      if ($display) {
        $assignment->show();
        if ($myTask->isTa() && $myTask->getEffort() == 'full')
          $iF = $iF + 1;
        if ($myTask->isTa() && $myTask->getEffort() == 'half')
          $iF = $iF + 0.5;
        if ($myTask->isTa() && $myTask->getEffort() == 'part')
          $iP = $iP + 1;
      }   
    }
    print "</table>";
    print "<p> TA openings ($option): <b>$iF</b> (full time)  <b>$iP</b> (part time).</p><br> \n";
  }
}

class Assignment
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

  public static function fromRow(array $row)   // 'constructor' using db query row
  {
    $instance = new self();
    $instance->fill($row);
    return $instance;
  }

  protected function fill(array $row)
  {
    // here we fill the content

    $this->term = $row[0];
    $this->task = $row[1];
    $this->person = $row[2];
    $this->evalO = $row[3];
  }

  public function show($option='full')
  {
    // print the full assignment
    $myTask = new TeachingTask($this->task);
    print "<tr>";
    print "<td> "
      . $this->term . ": &nbsp;</td><td>"
      . "<a href=\"/showTaskSummary?number=" . $myTask->getCourse(). "\">"
      . $myTask->getCourse()
      . "</a>"
      . "&nbsp;</td><td>"
      . $myTask->getType()    . "&nbsp;</td><td>"
      . $myTask->getEffort()  . "&nbsp;</td><td>"
      . $myTask->getTaType()  . "&nbsp;</td><td>";

    if ($option == 'simple')
      print "&nbsp;</td>\n";
    else
      print "<a href=\"/showTaSummary?email=" . $this->person . "\">"
        . $this->person
        . "</a>"
        . "&nbsp;</td><td>"
        . $myTask->generateId() . "&nbsp;</td>\n"
        . "<td>&nbsp; " . number_format($this->evalO,1,'.',',') . " &nbsp;</td>";
    print "</tr>\n";
  }

  // Property declaration
  public $term = '';
  public $task = '';
  public $person = '';
  public $evalO = -1;

}

?>
