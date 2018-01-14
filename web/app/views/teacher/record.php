<?php

include("app/views/teacher/header.php");

// make sure we are dealing with a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Tables.php");

$db = Dbc::getReader();

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

$studentEmail = 'undefined';
if (isset($_GET['ta']))
  $studentEmail = $_GET['ta'];

$evaluation = $_POST['Evaluation'];
$awardProposed = 0;
if (isset($_POST['Award']))
  $awardProposed = 1;
$citation = "";
if (isset($_POST['Citation']))
  $citation = $_POST['Citation'];

print '<article class="page">'."\n";
print '<h1>Recorded Evaluation</h1>';
print '<p>';
print "<b>Evaluator:</b> $email<br>\n";
print "<b>Evaluee:  </b> $studentEmail<br>\n";
print '<p>';
print "<b>Evaluation:</b><br> $evaluation</p>\n";
print '<p>';
print "<b>AwardProposed:</b> $awardProposed<br>\n";
print "<b>Proposed Citation:</b> $citation</p>\n";

// find active evaluations table
$activeTables = new Tables($db,"ActiveTables");
$evaluationsTable = $activeTables->getUniqueMatchingName('Evaluations');
$term = substr($evaluationsTable,-5,5);

// test whether evaluation is valid
if ($email != "" && $studentEmail != "" && $evaluation != "") {
  print '<p>Evaluation is valid. ';

  $sql = "select TeacherEmail,TaEmail from $evaluationsTable "
    . " where TeacherEmail='$email' and TaEmail='$studentEmail'";
  $rows = $db->query($sql);
  if ($rows->rowCount() < 1)   // no entry yet
    $sql = "insert into $evaluationsTable (TeacherEmail,TaEmail,EvalText,Award,Citation) values"
      . "('$email','$studentEmail','$evaluation',$awardProposed,'$citation')";
  else                     // need to update existing entry
    $sql = "update $evaluationsTable set EvalText='$evaluation', Award=$awardProposed, Citation='$citation' where "
      . " TeacherEmail='$email' and TaEmail='$studentEmail'";
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
  print '<p> Evaluation is NOT valid.<br>';
  print 'Please go back and make sure to select a student and write some text.</p>';
}

print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
