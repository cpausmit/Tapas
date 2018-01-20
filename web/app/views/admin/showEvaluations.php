<?php

include("app/views/admin/header.php");
// make sure we have a registered TA
if (! isMaster()) { 
  exitAccessError();
}
// command line parameters
$term = $_GET['term'];

include_once("app/models/Dbc.php");
include_once("app/models/Teacher.php");
include_once("app/models/Student.php");
include_once("app/models/Evaluation.php");

// connect to our database
$db = Dbc::getReader();

// get TA names
$rows = $db->query("select * from Students order by lastName");
$taNames = "";
foreach ($rows as $key => $row) {
  $student = Student::fromRow($row);
  $taNames[$student->email] = "$student->lastName, $student->firstName";
}

// get teacher names
$rows = $db->query("select * from Teachers order by lastName");
$teacherNames = "";
foreach ($rows as $key => $row) {
  $teacher = Teacher::fromRow($row);
  $teacherNames[$teacher->email] = "$teacher->lastName, $teacher->firstName";
}

// get evaluations
$evaluations = Evaluations::fromDb(Dbc::getReader(),$term);
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
