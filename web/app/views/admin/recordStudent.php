<?php

include("app/views/admin/header.php");

include("app/models/Dbc.php");
include("app/models/Student.php");

// make sure this is the master
if (! (isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print "<h1>New Student</h1>\n";

print " ABOUT TO RECORD <br>\n";
$student = Student::fresh();
$student->firstName = $_POST['firstName'];
$student->lastName = $_POST['lastName'];
$student->email = $_POST['email'];
$student->advisorEmail = $_POST['aaEmail'];
$student->supervisorEmail = $_POST['svEmail'];
$student->year = $_POST['year'];
$student->division = $_POST['division'];
$student->research = $_POST['research'];

$student->printSummary();

$db = Dbc::getReader();
$student->addToDb($db);

print "\n<br> SET<br>\n";

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

// connect to our database
$link = getLink();

// find term
$i = 0;
$terms = "";
$query = "show tables like 'Assignments%'";
$statement = $link->prepare($query);
$statement->execute();
$statement->bind_result($table);
while ($statement->fetch()) {
  $t = substr($table,-5,5);
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
$teachers = findTeacherNames($link);
if (! isset($teachers[$teacherEmail])) {  // check if this is a valid teacher (Faculties table)
  print ' EXIT - teacher email is not valid.';
  exitParameterError($teacherMail);
}
print ' Teacher valid.<br>';

$tas = findTaNames($link,$assignmentsTable);
if (! isset($tas[$taEmail])) {
  print ' EXIT - ta email is not valid.';
  exitParameterError($taMail);
}
print ' TA valid.<br>';
 
// test whether evaluation is valid
if ($teacherEmail != "" && $taEmail != "" && $evaluation != "") {
  print '<p>Evaluation is valid.<br>';

  // do we update or is it a new entry
  $query = "select TeacherEmail,TaEmail from $evaluationsTable "
    . "where TeacherEmail='$teacherEmail' and TaEmail='$taEmail'";
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
    $query = "insert into $evaluationsTable (TeacherEmail,TaEmail,EvalText,Award,Citation) values"
      . "('$teacherEmail','$taEmail','$evaluation',$awardProposed,'$citation')";
  else
    $query = "update $evaluationsTable set EvalText='$evaluation', Award=$awardProposed,"
      . " Citation='$citation' where "
      . " TeacherEmail='$teacherEmail' and TaEmail='$taEmail'";

  $statement = $link->prepare($query);
  $rc = $statement->execute();
  if (! $rc) {
    print " ERROR - could not register selection\n";
    $errNum = mysqli_errno($link); $errMsg = mysqli_error($link);
    print " ERROR - could not register selection: ErrNo=$errNum: $errMsg</p>\n";
    exit();
  }
  else {
    print 'Evaluation has been registered.</p>';
  }
}
else {
  print '<p>Evaluation is NOT valid.<br>';
  print 'Please go back and make sure to select a student and write some text.</p>';
}

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
