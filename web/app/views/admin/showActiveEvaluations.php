<?php

include("app/views/admin/header.php");

// make sure we have a registered TA
if (! isMaster()) { 
  exitAccessError();
}

include_once("app/models/Evaluation.php");
include_once("app/models/Student.php");
include_once("app/models/Tables.php");
include_once("app/models/Teacher.php");

// connect to our database
$students = Students::fromDb();
$teachers = Teachers::fromDb();

$activeTables = new Tables('ActiveTables');
$evalTable = $activeTables->getUniqueMatchingName('Evaluations');
$term = substr($evalTable,-5,5);
$evaluations = Evaluations::fromDb($term);
$n = sizeof($evaluations->list);

// get TA names
$taNames = "";
foreach ($students->list as $key => $student)
  $taNames[$student->email] = "$student->lastName, $student->firstName";

// get teacher names
$teacherNames = "";
foreach ($teachers->list as $key => $teacher)
  $teacherNames[$teacher->email] = "$teacher->lastName, $teacher->firstName";
 
// Present results
print '<article class="page">'."\n";
print '<h1>All Active TA Evaluations</h1>'."\n";
print ' '."\n";
print " $n evaluations found. <br>\n";
foreach ($evaluations->list as $key => $evaluation)
  $evaluation->printEvaluation($taNames,$teacherNames);
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
