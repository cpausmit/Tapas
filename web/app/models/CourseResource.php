<?php

// create table CourseResources (
//   Term           char(5)  not NULL,
//   Number         char(10) not NULL,
//   NumAdmins      int      default 0,
//   NumLecturers   int      default 0,
//   NumRecitators  int      default 0,
//   NumFullRecTas  int      default 0,
//   NumHalfRecTas  int      default 0,
//   NumFullUtilTas int      default 0,
//   NumHalfUtilTas int      default 0,
//   NumPartUtilTas int      default 0
// );
// alter table CourseResources add constraint onePerTerm unique(Term, Number);


function insertEmptySlot($db,$term,$slotId)
{
  // insert a particular slot id into the assignments table if it does not yet exist
  $vals = sprintf("('%s','EMPTY')",$slotId);
  $sql = "insert into Assignments$term values $vals;";
  $rc = $db->Exec($sql);
  if (!$rc) {
    $errorArray = $db->errorInfo();
    if ($errorArray[0] == 23000 && $errorArray[1] == 1062) {
      print "<!-- WARNING -- duplicate entry.<br> --> \n";
    }
    else {
      print "<br>\n ERROR -- PDO::errorInfo():\n";
      print_r($db->errorInfo());
    }
  }
  else
    print "<br>Inserted new slot: $slotId ($sql)";

  return;
}

class CourseResources
{
  // Property declaration
  public $list = array();
  public $nTeachers = '';
  public $nFullTas = '';
  public $nPartTas = '';

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank course
    $instance = new self();
    return $instance;
  }

  public function addCourseResource($courseResource)
  {
    if (!isset($list[$courseResource->number])) {
      $this->list[$courseResource->number] = $courseResource;
      $this->nTeachers += $courseResource->numLecturers + $courseResource->numRecitators;
      $this->nFullTas += $courseResource->numFullRecTas + $courseResource->numFullUtilTas
        +  0.5 * ($courseResource->numHalfRecTas + $courseResource->numHalfUtilTas);
      $this->nPartTas += $courseResource->numPartUtilTas;
    }
    else {
      print " ERROR - trying to add a course resource twice.<br>\n";
    }
    
    return;
  }

  public function showSummary()
  {
    // print a summery of the course resources
    print " $this->nTeachers teachers,".
          " $this->nFullTas full time TAs,".
          " $this->nPartTas part time TAs.<br>\n";
  }

  public function printAssignments()
  {
    // loop through course resources and print the resulting assignments

    foreach ($this->list as $key => $courseResource) {
      print "<br> Course: <b>$courseResource->number </b>\n";
      $courseResource->printAssignments();
    }

    return;
  }

  public function registerAssignments($db)
  {
    // loop through course resources and make missing empty slots in the database

    foreach ($this->list as $key => $courseResource)
      $courseResource->registerAssignments($db);

    return;
  }
}

class CourseResource
{
  // Property declaration

  public $term = '';
  public $number = '';
  public $numAdmins = 0;
  public $numLecturers = 0;
  public $numRecitators = 0;
  public $numFullRecTas = 0;
  public $numHalfRecTas = 0;
  public $numFullUtilTas = 0;
  public $numHalfUtilTas = 0;
  public $numPartUtilTas = 0;

  // Declare a public constructor
  public function __construct() { }
  public function __destruct() { }

  public static function fresh()
  {
    // 'constructor' returns blank course
    $instance = new self();
    return $instance;
  }

  public static function fromTermAndNumber($db,$term,$number) // 'constructor' - term and number
  {
    $instance = new self();
    $sql = "select * from CourseResources where Term = '".$term."' and Number = '".$number."'";
    $courseResources = $db->query($sql);
    foreach ($courseResources as $key => $row) {
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

    $this->term = $row[0];
    $this->number = $row[1];
    $this->numAdmins = intval($row[2]);
    $this->numLecturers = intval($row[3]);
    $this->numRecitators = intval($row[4]);
    $this->numFullRecTas = intval($row[5]);
    $this->numHalfRecTas = intval($row[6]);
    $this->numFullUtilTas = intval($row[7]);
    $this->numHalfUtilTas = intval($row[8]);
    $this->numPartUtilTas = intval($row[9]);
  }

  public function printAssignments()
  {
    // print one row of a table with the relvant information

    for ($i=1; $i<=$this->numAdmins; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-Adm-".$i." ";
    }
    for ($i=1; $i<=$this->numLecturers; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-Lec-".$i." ";
    }
    for ($i=1; $i<=$this->numRecitators; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-Rec-".$i." ";
    }
    for ($i=1; $i<=$this->numFullRecTas; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-TaFR-".$i." ";
    }
    for ($i=1; $i<=$this->numFullUtilTas; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-TaFU-".$i." ";
    }
    for ($i=1; $i<=$this->numHalfRecTas; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-TaHR-".$i." ";
    }
    for ($i=1; $i<=$this->numHalfUtilTas; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-TaHU-".$i." ";
    }
    for ($i=1; $i<=$this->numPartUtilTas; $i++) {
      if ($i==1)
        print "<br>\n";
      print $this->term."-".$this->number."-TaPU-".$i." ";
    }
    print "<br>";
  }

  public function printTableRow($open)
  {
    // print one row of a table with the relvant information

    print "<tr>\n";
    print "<td align=center>&nbsp; $this->term &nbsp;</td>";
    print "<td align=center>&nbsp; $this->number &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numAdmins &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numLecturers &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numRecitators &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numFullRecTas &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numHalfRecTas &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numFullUtilTas &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numHalfUtilTas &nbsp;</td>";
    print "<td align=center>&nbsp; $this->numPartUtilTas &nbsp;</td>";
    if (!$open)
      print "</tr>\n";
  }

  public function printTableHeader($open)
  {
    // print header of the table

    print "<tr>\n";
    print "<th>&nbsp Term &nbsp;</th>";
    print "<th>&nbsp Number &nbsp;</th>";
    print "<th>&nbsp Admins &nbsp;</th>";
    print "<th>&nbsp Lecturers &nbsp;</th>";
    print "<th>&nbsp Recitators &nbsp;</th>";
    print "<th>&nbsp Full TA(R) &nbsp;</th>";
    print "<th>&nbsp Half TA(R) &nbsp;</th>";
    print "<th>&nbsp Full TA(U) &nbsp;</th>";
    print "<th>&nbsp Half TA(U) &nbsp;</th>";
    print "<th>&nbsp Part TA(U) &nbsp;</th>";
    if (!$open)
      print "</tr>\n";
  }

  public function isFresh()
  {
    // say whether this is a fresh record
    return ($this->term == '');
  }

  public function addToDb($db)
  {
    // adding the given course instance to the database

    // check for duplicate
    $vals = sprintf("('%s','%s',%d,%d,%d,%d,%d,%d,%d,%d)",
                    $this->term,$this->number,
                    $this->numAdmins,$this->numLecturers,$this->numRecitators,
                    $this->numFullRecTas,$this->numHalfRecTas,
                    $this->numFullUtilTas,$this->numHalfUtilTas,
                    $this->numPartUtilTas);
    $sql = " insert into CourseResources values $vals";
    print " ADDING TO THE DATABASE<br>\n";
    $db->Exec($sql);
  }

  public function updateDb($db)
  {
    // updating the given course instance to the database

    $form = "Term = '%s', Number = '%s', NumAdmins = %d, NumLecturers = %d, NumRecitators = %d,".
            " NumFullRecTas = %d, NumHalfRecTas = %d, NumFullUtilTas = %d, NumHalfUtilTas = %d,".
            " NumPartUtilTas = %d";
    $vals = sprintf($form,
                    $this->number,$this->name,
                    $this->numAdmins,$this->numLecturers,$this->numRecitators,
                    $this->numFullRecTas,$this->numHalfRecTas,
                    $this->numFullUtilTas,$this->numHalfUtilTas,
                    $this->numPartUtilTas);

    $sql = " update CourseResources set $vals where".
           " Term = '$this->term' and Number = '$this->number';";
    $db->Exec($sql);
  }

  public function registerAssignments($db)
  {
    // register all assignments: if empty, generate new slot, otherwise do nothing

    for ($i=1; $i<=$this->numAdmins; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-Adm-".$i);
    for ($i=1; $i<=$this->numLecturers; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-Lec-".$i);
    for ($i=1; $i<=$this->numRecitators; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-Rec-".$i);
    for ($i=1; $i<=$this->numFullRecTas; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-TaFR-".$i);
    for ($i=1; $i<=$this->numFullUtilTas; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-TaFU-".$i);
    for ($i=1; $i<=$this->numHalfRecTas; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-TaHR-".$i);
    for ($i=1; $i<=$this->numHalfUtilTas; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-TaHU-".$i);
    for ($i=1; $i<=$this->numPartUtilTas; $i++)
      insertEmptySlot($db,$this->term,$this->term."-".$this->number."-TaPU-".$i);
    return;
  }
}

?>
