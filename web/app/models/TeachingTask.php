<?php

class TeachingTask
{

  // Declare a public constructor
  public function __construct($id) {

    // store the id
    $this->id = $id;
 
    // decode the id into its pieces
    $f = explode("-",$id);
    if (count($f) == 4) {
      $this->term = $this->convertTerm(substr($f[0],0,1));
      $this->year = $this->extractYear($f[0]); 
      $this->course = $f[1];
      $this->type = $this->findType($f[2]);
      if ($this->type == 'TA') {
	$this->effort = $this->convertEffort(substr($f[2],2,1));
	$this->taType = substr($f[2],3,1);
      }
      else {
	$this->effort = 'full';
	$this->taType = 'none';
      }
      $this->instance = $f[3];
    }
    else {
      print ' FAILURE -- incorrect ID given: ' .$id;
    }
  }

  public function __destruct() {
    //echo 'Destroying: ', $this->name, PHP_EOL;
  }

  // Simple accessors
  public function getId() { return $this->id; }
  public function getTerm() { return $this->term; }
  public function getYear() { return $this->year; }
  public function getCourse() { return $this->course; }
  public function getEffort() { return $this->effort; }
  public function getType() { return $this->type; }
  public function getTaType() { return $this->taType; }
  public function getInstance() { return $this->instance; }

  public function updateYear($year) {
    if ($year != $this->year) {
      print " Updating: $this->year --> $year<br>\n";
      $this->year = $year;
    }
  }

  public function generateId() {
    $effort = strtoupper($this->effort[0]);
    $term = strtoupper($this->term[0]);
    $id = $term . $this->year . "-" . $this->course . "-";
    if ($this->isTa())
      $id = $id . "Ta" . $effort . $this->taType;
    else
      $id = $id . substr($this->type,0,3);
    $id = $id . "-" . $this->instance;
    
    return $id;
  }

  public function isTa() { return ($this->type == 'TA'); }

  // More involved classes
  public function printTask() {
    print ' ' . $this->year . ' ' . $this->term . ', ' . $this->course . ', '
      . $this->type . ', ' . $this->effort . ' time, ' . $this->taType . "<br>\n"; 
  }
  public function printTaskWithLink() {
    print ' ' . $this->year . ' ' . $this->term
      . ', <a href="/showTaskSummary?number=' . $this->course . '">' . $this->course . '</a>, '
      . $this->type . ', ' . $this->effort . ' time, ' . $this->taType . "<br>\n"; 
  }
  public function getTaTask() {
    return $this->year . ' ' . $this->term . ', ' . $this->course . ', '
      . $this->effort . ' time, ' . $this->taType;
  }
  public function printTaTask() {
    print $this->getTaTask() . "<br>\n"; 
  }

  private function convertTerm($letter) {
    $term = '';
    if ($letter == "F")
      $term = 'Fall';
    elseif ($letter == "S")
      $term = 'Spring';
    elseif ($letter == "I")
      $term = 'IAP';
    else
      print ' ERROR - not a valid term found : ' . $letter;

    return $term; 
  }

  private function extractYear($letter) {
    $year = substr($letter,1);
    if (strlen($year) == 2)
      $year = "20". "$year";
    
    return $year;
  }

  private function findType($letter) {
    $type = '';
    if     ($letter == "Lec")
      $type = 'Lecturer';
    elseif ($letter == "Adm")
      $type = 'Admin';
    elseif ($letter == "Rec")
      $type = 'Recitator';
    elseif (substr($letter,0,2) == "Ta")
      $type = 'TA';
    else
      print ' ERROR - not a valid type found : ' . $letter;

    return $type; 
  }

  private function convertEffort($letter) {
    $effort = '';
    if ($letter == "F")
      $effort = 'full';
    elseif ($letter == "H")
      $effort = 'half';
    elseif ($letter == "P")
      $effort = 'part';
    else
      print ' ERROR - not a valid effort found : ' . $letter;

    return $effort; 
  }

  // property declaration
  private $id = 'TYY-C.CCC-TaET-I';
  private $term = 'T';
  private $year = 'YY';
  private $course = 'C.CCC';
  private $effort = 'E';
  private $type = 'T';
  private $instance = 'I';
  private $taType = 'Ta';
}

?>
