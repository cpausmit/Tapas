<?php

include("app/views/admin/header.php");

include("app/models/Dbc.php");
include("app/models/Student.php");

// make sure this is the master
if (! (isMaster())) { 
  exitAccessError();
}

$db = Dbc::getReader();
$student = Student::fromEmail($db,$_POST['email']);
$new = $student->isFresh();

print '<article class="page">'."\n";
if ($new)
  print "<h1>New Student</h1>\n";
else
  print "<h1>Update Existing Student</h1>\n";

// Set all relevant variables
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
if ($new)
  $student->addToDb($db);
else
  $student->updateDb($db);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
