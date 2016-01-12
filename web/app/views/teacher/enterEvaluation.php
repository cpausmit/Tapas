<?php

include("app/views/teacher/header.php");

// make sure we have a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Enter TA Evaluation</h1>'."\n";
print ' '."\n";

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
$studentEmail = 'undefined';
if (isset($_POST['ta']))
  $studentEmail = $_POST['ta'];

// connect to our database
$link = getLink();
$name = findStudentName($link,$studentEmail);

// find existing evaluation
$active = findActiveTable($link,'Evaluations');
print "Active evaluations table: $active[0]</p>";
$query = "select TeacherEmail,TaEmail,EvalText,Award,Citation from $active[0] "
  . "where TeacherEmail='$email' and TaEmail='$studentEmail'";
//print " Query: $query";
$statement = $link->prepare($query);
$rc = $statement->execute();
if (! $rc) {
  $errNum = mysqli_errno($link);
  $errMsg = mysqli_error($link);
  print " ERROR - could not register selection: ErrNo=$errNum: $errMsg\n";
  exit();
}
$statement->bind_result($teacherEmail,$taEmail,$evalText,$award,$citation);
while ($statement->fetch()) {
  print " ";
}

// start the form
print '<p>';
print "<form action=\"/record?ta=$studentEmail\" method=\"post\">\n";
print "Write your evaluation for $name below.\n";
print '<textarea  style="font-family: arial, verdana, sans-serif; font-size: 20px; color: black; background-color: white" name="Evaluation" rows=8 cols=80>'. $evalText .'</textarea>';
if ($award == 0)
  print '<input type="checkbox" name="Award" value="1"> Propose award?';
else
  print '<input type="checkbox" name="Award" value="1"checked> Propose award?';
print '&nbsp; if yes, please add a short citation for the potential award below.<br>';
print '<textarea style="font-family: arial, verdana, sans-serif; font-size: 20px; color: black; background-color: white" name="Citation" rows=1 cols=40>' . $citation . '</textarea>';
print '<input type="submit" value="submit your evaluation" />'."\n";
print '</form>'."\n";
print '</p>'."\n";

print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
