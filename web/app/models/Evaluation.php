<?php

// mysql> describe EvaluationsF2015;
// +--------------+------------+------+-----+---------+-------+
// | Field        | Type       | Null | Key | Default | Extra |
// +--------------+------------+------+-----+---------+-------+
// | Term         | char(5)    | YES  |     | NULL    |       | 
// | TeacherEmail | char(40)   | YES  |     | NULL    |       | 
// | TaEmail      | char(40)   | YES  |     | NULL    |       | 
// | Award        | tinyint(4) | YES  |     | 0       |       | 
// | EvalText     | text       | YES  |     | NULL    |       | 
// | Citation     | text       | YES  |     | NULL    |       | 
// +--------------+------------+------+-----+---------+-------+

include_once("app/models/Dbc.php");

class Evaluations
{
  // Property declaration
  public $list = array();

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank evaluation
    $instance = new self();
    return $instance;
  }

  public static function fromDb($term)
  {
    // 'constructor' returns full list of evaluations
    $instance = new self();

    if ($term == 'ALL')
      $rows = Dbc::getReader()->query("select * from Evaluations");
    else
      $rows = Dbc::getReader()->query("select * from Evaluations where Term='$term'");
    
    foreach ($rows as $key => $row)
      $instance->add(Evaluation::fromRow($row));
    
    return $instance;
  }

  public function add($evaluation)
  {
    if (!isset($this->list[$evaluation->key()]))
      $this->list[$evaluation->key()] = $evaluation;
    else
      print " ERROR - trying to add an evaluation twice.<br>\n";
    
    return;
  }
}

class Evaluation
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
    $this->teacherEmail = $row[1];
    $this->taEmail = $row[2];
    $this->award = intval($row[3]);
    $this->evalText = $row[4];
    $this->citation = $row[5];
  }

  public function key()
  {
    // print the full evaluation
    $key = "$this->term:$this->teacherEmail:$this->taEmail";
    return $key;
  }

  public function printEvaluation($taNames,$teacherNames)
  {
    // print the full evaluation

    if (array_key_exists($this->taEmail,$taNames))
      $taName = $taNames[$this->taEmail];
    else
      $taName = "<b>NO NAME FOUND IN DATABASE - FIX IT!</b>";

    if (array_key_exists($this->teacherEmail,$teacherNames))
      $teacherName = $teacherNames[$this->teacherEmail];
    else
      $teacherName = "<b>NO NAME FOUND IN DATABASE - FIX IT!</b>";

    print '<hr>';
    print "<h2>Evaluation for $taName (Eml: $this->taEmail, Term: $this->term)</h2>";
    print "<p>\n";
    print "<b>Evaluated by:</b>";
    print " $teacherName ($this->teacherEmail)<br>\n";
    print "<b>AwardProposed:</b> $this->award<br>\n";
    print "<b>Proposed Citation:</b> $this->citation<br>\n";
    print "<b>Evaluation:</b><br> $this->evalText</p>\n";
  }

  // Property declaration
  public $term = '';
  public $teacherEmail = 'EMPTY@mit.edu';
  public $taEmail = 'EMPTY@mit.edu';
  public $award = 0;
  public $evalText = "";
  public $citation = "";

}

?>
