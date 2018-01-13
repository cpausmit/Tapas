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

// find active evaluations table
$evalTable = 'Evaluations'.$term;

// get evaluations
$i = 0;
$evaluations = "";
$rows = $db->query("select * from $evalTable order by TaEmail");
foreach ($rows as $key => $row) {
  $evaluation = Evaluation::fromRow($row);
  $evaluations[$i] = $evaluation;
  $i = $i + 1;
}
 
// Present the results

print '<article class="page">'."\n";
print "<h1>All TA Evaluations for Term $term</h1>\n";
print "\n";

// loop through evaluations and print

if ($evaluations == "") 
  print " No evaluations found in this term ($term).";
else
  foreach ($evaluations as $key => $evaluation) {
    $evaluation->printEvaluation($taNames,$teacherNames);
  }
  
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
