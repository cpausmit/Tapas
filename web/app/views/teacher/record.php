<?php

include("app/views/teacher/header.php");

// make sure we are dealing with a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Dbc.php");
include_once("app/models/Tables.php");

$email = strtolower($_SERVER['eppn']);

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
print "<b>Proposed -- Citation:</b> $citation</p>\n";

// find active evaluations table
$activeTables = new Tables("ActiveTables");
$evaluationsTable = $activeTables->getUniqueMatchingName('Evaluations');
$term = substr($evaluationsTable,-5,5);

// test whether evaluation is valid
if ($email != "" && $studentEmail != "" && $evaluation != "") {
  print '<p>Evaluation is valid. ';

  $sql = "select TeacherEmail,TaEmail from Evaluations "
      . " where Term='$term' and TeacherEmail='$email' and TaEmail='$studentEmail'";
  $rows = Dbc::getReader()->query($sql);
  if ($rows->rowCount() < 1)   // no entry yet
    $sql = "insert into Evaluations (Term,TeacherEmail,TaEmail,EvalText,Award,Citation) values('$term','$email','$studentEmail',:EvalText,$awardProposed,:Citation)";    
    //$sql = "insert into Evaluations (Term,TeacherEmail,TaEmail,EvalText,Award,Citation) values"
    //  . "('$term','$email','$studentEmail','$evaluation',$awardProposed,'$citation')";
  else                         // need to update existing entry
    $sql = "update Evaluations  set EvalText=:EvalText, Award=$awardProposed, Citation=:Citation where Term='$term' and TeacherEmail='$email' and TaEmail='$studentEmail'";
    //$sql = "update Evaluations set EvalText='$evaluation', Award=$awardProposed, Citation='$citation' where "
    //  . " Term='$term' and TeacherEmail='$email' and TaEmail='$studentEmail'";

  // execute
  try {
    $safe_sql = Dbc::getReader()->prepare($sql);
    $safe_sql->bindParam(':EvalText', $evaluation, PDO::PARAM_STR);
    $safe_sql->bindParam(':Citation', $citation, PDO::PARAM_STR);
    $safe_sql->execute();
    
    //Dbc::getReader()->exec($sql);
    print "Evaluation has been registered.</p>";
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
