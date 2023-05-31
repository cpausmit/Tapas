<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Assignment.php");
include_once("app/models/Semester.php");
include_once("app/models/Teacher.php");

print '<article class="page">'."\n";
print '<h1>Recorded Evaluation</h1>';

$email = strtolower($_SERVER['eppn']);

$term = $_POST['term'];
$teacherEmail = $_POST['teacherEmail'];
$taEmail = $_POST['taEmail'];
$evaluation = addslashes($_POST['evaluation']);
$awardProposed = 0;
if (isset($_POST['award']))
  $awardProposed = 1;
$citation = "";
if (isset($_POST['citation']))
  $citation = addslashes($_POST['citation']);

print '<p>';
print "<b>Semester:</b> $term<br>\n";
print "<b>Evaluator:</b> $teacherEmail<br>\n";
print "<b>Evaluee:  </b> $taEmail<br>\n";
print '<p>';
print "<b>Evaluation:</b><br> $evaluation</p>\n";
print '<p>';
print "<b>AwardProposed:</b> $awardProposed<br>\n";
print "<b>Proposed Citation:</b> $citation</p>\n";

// find terms
$semesters = Semesters::fromDb();
if (! isset($semesters->list[$term])) {  // check if this is a valid term
  print ' EXIT - term is not valid.';
  exitParameterError($term);
}
print ' Term valid.<br>';

// find the tables
$evaluationsTable = 'Evaluations'; //print " Evaluations from $evaluationsTable.<br>";

// find teacher and TA
$teachers = Teachers::fromDb();
if (! isset($teachers->list[$teacherEmail])) {  // check if this is a valid teacher (Teachers table)
  print " EXIT - teacher email is not valid.<br>";
  exitParameterError($teacherMail);
}
$teacher = $teachers->list[$teacherEmail];
print ' Teacher valid.<br>';

$assignments = Assignments::fromDb($term);
$test = "EMPTY";
foreach ($assignments->list as $task => $assignment) {
    //print " $taEmail --> $assignment->person ";
  if (strcmp($taEmail,$assignment->person) == 0) {
      //print " MATCH <br>";
    $test = $assignment->person;
  }
  //print " no <br>";
}
if (strcmp($test,"EMPTY") == 0) {
  print " EXIT - ta email is not valid.<br>";
  exitParameterError($taEmail);
}
print ' TA valid.<br>';

// test whether evaluation is valid
if ($teacherEmail != "" && $taEmail != "" && $evaluation != "") {
  print '<p>Evaluation is valid.<br>';
  
  // do we update or is it a new entry
  $sql = "select TeacherEmail,TaEmail from $evaluationsTable "
    . " where Term='$term' and TeacherEmail='$teacherEmail' and TaEmail='$taEmail'";
  $rows = Dbc::getReader()->query($sql);
  if ($rows->rowCount() < 1) { // no entry yet
    $sql = "insert into $evaluationsTable (Term,TeacherEmail,TaEmail,EvalText,Award,Citation) values"
      . "('$term','$teacherEmail','$taEmail','$evaluation',$awardProposed,'$citation')";
  }
  else {                   // need to update existing entry
    $sql = "update $evaluationsTable set EvalText='$evaluation', Award=$awardProposed,"
      . " Citation='$citation' where "
      . " Term='$term' and TeacherEmail='$teacherEmail' and TaEmail='$taEmail'";
  }
  // execute
  try {
    Dbc::getReader()->exec($sql);
    print 'Evaluation has been registered.</p>';
  }
  catch (PDOException $e) {
    print " ERROR - could not register selection: ".$e->getMessage()."<br>\n";
  }
}
else {
  print '<p>Evaluation is NOT valid.<br>';
  print 'Please go back and make sure to select a student and write some text.</p>';
}

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
