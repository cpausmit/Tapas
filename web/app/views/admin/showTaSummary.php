<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

// command line arguments
$email = $_GET['email'];

// load database and models
include_once("app/models/Assignment.php");
include_once("app/models/Evaluation.php");
include_once("app/models/Student.php");
include_once("app/models/Teacher.php");
include_once("app/models/TeachingTask.php");

// create an instance
$students = Students::fromDb();
$ta = $students->list[$email];
$teachers = Teachers::fromDb();

$taNames = array();
foreach ($students->list as $key => $student)
  $taNames[$student->email] = "$student->firstName $student->lastName";

$teacherNames = array();
foreach ($teachers->list as $key => $teacher)
  $teacherNames[$teacher->email] = "$teacher->firstName $teacher->lastName";

print '<article class="page">'."\n";
print "<hr>\n";
$ta->printSummary();
print "\n";
print "<hr>\n";

// loop through all assignment tables and find our candidate
$assignments = Assignments::fromDb('ALL');
print '<ul>';
foreach ($assignments->list as $key => $assignment) {
  if ($assignment->person == $email) {
    print '<li>';
    $assignment->show('simple');
  }
}
print '</ul>';

// loop through evaluation table and find our candidate
$evaluations = Evaluations::fromDb('ALL');
print '<ul>';
foreach ($evaluations->list as $key => $evaluation) {
  if ($evaluation->taEmail == $email) {
    print '<li>';
    $evaluation->printEvaluation($taNames,$teacherNames);
  }
}
print '</ul>';

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
