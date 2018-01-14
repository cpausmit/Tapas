<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Teacher.php");
include_once("app/models/Ta.php");

$db = Dbc::getReader();

print '<article class="page">'."\n";
print '<h1>Recorded Evaluation</h1>';

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

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
$i = 0;
$index = array();
$rows = $db->query("show tables like 'Assignments%'");
foreach ($rows as $key => $row) {
  $t = substr($row[0],-5,5);
  $index[$t] = $i;
  $i = $i + 1;
}
if (! isset($index[$term])) {  // check if this is a valid term
  print ' EXIT - term is not valid.';
  exitParameterError($term);
}
print ' Term valid.<br>';

// find the tables
$evaluationsTable = 'Evaluations'.$term; //print " Evaluations from $evaluationsTable.<br>";
$assignmentsTable = 'Assignments'.$term; //print " Assignments from $assignmentsTable.<br>";

// find teacher and TA
$teachers = Teachers::fromDb($db);
if (! isset($teachers->list[$teacherEmail])) {  // check if this is a valid teacher (Teachers table)
  print " EXIT - teacher email is not valid.<br>";
  exitParameterError($teacherMail);
}
$teacher = $teachers->list[$teacherEmail];
print ' Teacher valid.<br>';

$tas = Tas::fromDb($db,$term);
if (! isset($tas->list[$taEmail])) {
  print " EXIT - ta email is not valid.<br>";
  exitParameterError($taMail);
}
$ta = $tas->list[$taEmail];
print ' TA valid.<br>';

// test whether evaluation is valid
if ($teacherEmail != "" && $taEmail != "" && $evaluation != "") {
  print '<p>Evaluation is valid.<br>';
  
  // do we update or is it a new entry
  $sql = "select TeacherEmail,TaEmail from $evaluationsTable "
    . " where TeacherEmail='$teacherEmail' and TaEmail='$taEmail'";
  $rows = $db->query($sql);
  if ($rows->rowCount() < 1) { // no entry yet
    $sql = "insert into $evaluationsTable (TeacherEmail,TaEmail,EvalText,Award,Citation) values"
      . "('$teacherEmail','$taEmail','$evaluation',$awardProposed,'$citation')";
  }
  else {                   // need to update existing entry
    $sql = "update $evaluationsTable set EvalText='$evaluation', Award=$awardProposed,"
      . " Citation='$citation' where "
      . " TeacherEmail='$teacherEmail' and TaEmail='$taEmail'";
  }
  // execute
  try {
    $db->exec($sql);
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
