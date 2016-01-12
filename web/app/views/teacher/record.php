<?php

include("app/views/teacher/header.php");

// make sure we are dealing with a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Recorded Evaluation</h1>';

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

print '<p>';
print "<b>Evaluator:</b> $email<br>\n";
print "<b>Evaluee:  </b> $studentEmail<br>\n";
print '<p>';
print "<b>Evaluation:</b><br> $evaluation</p>\n";
print '<p>';
print "<b>AwardProposed:</b> $awardProposed<br>\n";
print "<b>Proposed Citation:</b> $citation</p>\n";

// connect to our database
$link = getLink();
// find the active table
$active = findActiveTable($link,'Evaluations');
//print "Active evaluations table: $active[0]</p>";

// test whether evaluation is valid
if ($email != "" && $studentEmail != "" && $evaluation != "") {
  print '<p>Evaluation is valid.';

  // do we update or is it a new entry
  $query = "select TeacherEmail,TaEmail from $active[0] "
    . "where TeacherEmail='$email' and TaEmail='$studentEmail'";
  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (! $rc) {
    $errNum = mysqli_errno($link); $errMsg = mysqli_error($link);
    print " ERROR - could not register selection: ErrNo=$errNum: $errMsg\n";
    exit();
  }
  $statement->bind_result($teacherEmail,$taEmail);
  $empty = true;
  while ($statement->fetch()) {
    $empty = false;
  }

  if ($empty)
    $query = "insert into $active[0] (TeacherEmail,TaEmail,EvalText,Award,Citation) values"
      . "('$email','$studentEmail','$evaluation',$awardProposed,'$citation')";
  else
    $query = "update $active[0] set EvalText='$evaluation', Award=$awardProposed, Citation='$citation' where "
      . " TeacherEmail='$email' and TaEmail='$studentEmail'";

  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (! $rc) {
    print " ERROR - could not register selection\n";
    $errNum = mysqli_errno($link); $errMsg = mysqli_error($link);
    print " ERROR - could not register selection: ErrNo=$errNum: $errMsg\n";
    exit();
  }
  else {
    print 'Evaluation has been registered.</p>';
  }
}
else {
  print '<p> Evaluation is NOT valid.<br>';
  print 'Please go back and make sure to select a student and write some text.</p>';
}

print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
