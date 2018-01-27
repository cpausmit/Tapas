<?php

include("app/views/admin/header.php");
// make sure we have a registered TA
if (! isMaster()) { 
  exitAccessError();
}
// command line parameters
$term = $_GET['term'];

include_once("app/models/Teacher.php");
include_once("app/models/Student.php");
include_once("app/models/Evaluation.php");

// get TA names
$students = Students::fromDb();
$taNames = array();
foreach ($students->list as $key => $student)
  $taNames[$student->email] = "$student->lastName, $student->firstName";

// get teacher names
$teachers = Teachers::fromDb();
$teacherNames = array();
foreach ($teachers->list as $key => $teacher)
  $teacherNames[$teacher->email] = "$teacher->lastName, $teacher->firstName";

// get evaluations
$evaluations = Evaluations::fromDb($term);
$n = sizeof($evaluations->list);
 
// Present the results
print '<article class="page">'."\n";
print "<h1>All TA Evaluations for Term $term</h1>\n";
print "\n";
print " $n evaluations found. <br>\n";
foreach ($evaluations->list as $key => $evaluation)
  $evaluation->printEvaluation($taNames,$teacherNames);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
