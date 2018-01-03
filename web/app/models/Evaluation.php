<?php

// mysql> describe EvaluationsF2015;
// +--------------+------------+------+-----+---------+-------+
// | Field        | Type       | Null | Key | Default | Extra |
// +--------------+------------+------+-----+---------+-------+
// | TeacherEmail | char(40)   | YES  |     | NULL    |       | 
// | TaEmail      | char(40)   | YES  |     | NULL    |       | 
// | Award        | tinyint(4) | YES  |     | 0       |       | 
// | EvalText     | text       | YES  |     | NULL    |       | 
// | Citation     | text       | YES  |     | NULL    |       | 
// +--------------+------------+------+-----+---------+-------+

class Evaluations
{
  // Property declaration
  public $list = '';

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank evaluation
    $instance = new self();
    return $instance;
  }

  public static function fromDb($db,$term)
  {
    // 'constructor' returns full list of evaluations
    $instance = new self();
    $evaluationRows = $db->query("select * from Evaluations$term");
    foreach ($evaluationRows as $key => $row)
      $instance->addEvaluation(Evaluation::fromRow($row));
    
    return $instance;
  }

  public function addEvaluation($evaluation)
  {
    $key = "$evaluation->teacherEmail:$evaluation->taEmail";
    if (!isset($this->list[$key]))
      $this->list[$key] = $evaluation;
    else
      print " ERROR - trying to add a evaluation twice.<br>\n";
    
    return;
  }
}

class Evaluation
{

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fromRow(array $row)   // 'constructor' using db query row
  {
    $instance = new self();
    $instance->fill($row);
    return $instance;
  }

  protected function fill(array $row)
  {
    // here we fill the content

    $this->teacherEmail = $row[0];
    $this->taEmail = $row[1];
    $this->award = intval($row[2]);
    $this->evalText = $row[3];
    $this->citation = $row[4];
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
    print "<h2>Evaluation for $taName ($this->taEmail)</h2>";
    print "<p>\n";
    print "<b>Evaluated by:</b>";
    print " $teacherName ($this->teacherEmail)<br>\n";
    print "<b>AwardProposed:</b> $this->award<br>\n";
    print "<b>Proposed Citation:</b> $this->citation<br>\n";
    print "<b>Evaluation:</b><br> $this->evalText</p>\n";
  }

  // Simple accessors
  public function getTeacherEmail() { return $this->teacherEmail; }
  public function getTaEmail() { return $this->taEmail; }
  public function getAward() { return $this->award; }
  public function getEvalText() { return $this->evalText; }
  public function getCitation() { return $this->citation; }

  // Property declaration
  public $teacherEmail = 'EMPTY@mit.edu';
  public $taEmail = 'EMPTY@mit.edu';
  public $award = 0;
  public $evalText = "";
  public $citation = "";

}

?>
