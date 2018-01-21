<?php

include("app/views/teacher/header.php");

// make sure we have a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Evaluation.php");
include_once("app/models/Student.php");
include_once("app/models/Teacher.php");
include_once("app/models/Tables.php");

print '<article class="page">'."\n";
print '<h1>Review TA Evaluations</h1>'."\n";
print ' '."\n";

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

$students = Students::fromDb();
$teachers = Teachers::fromDb();
$activeTables = new Tables("ActiveTables");
$evaluationsTable = $activeTables->getUniqueMatchingName('Evaluations');
$term = substr($evaluationsTable,-5,5);

$evaluations = Evaluations::fromDb($term);
$sql = "select Term,TeacherEmail,TaEmail,Award,EvalText,Citation from Evaluations "
    . "where Term='$term' and TeacherEmail='$email'";
$rows = Dbc::getReader()->query($sql);
if ($rows->rowCount() < 1) {
  print " ERROR - could not find evaluation for sql: $sql\n";
  exit();
}
else if ($rows->rowCount() > 1) {
  print " ERROR - more evaluation than one registered for sql: $sql\n";
  exit();
}

$evaluation = null;
foreach ($rows as $key => $row)
  $evaluation = Evaluation::fromRow($row);

if ($evaluation == null)
  print ' No evaluations found in this term.';
else {
  $student = $students->list[$evaluation->taEmail];
  $teacher = $teachers->list[$evaluation->teacherEmail];
  $taNames[$evaluation->taEmail] = $student->firstName." ".$student->lastName;
  $teacherNames[$evaluation->teacherEmail] = $teacher->firstName." ".$teacher->lastName;
  $evaluation->printEvaluation($taNames,$teacherNames);
}
  
print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
