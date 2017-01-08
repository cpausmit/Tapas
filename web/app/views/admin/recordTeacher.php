<?php

include("app/views/admin/header.php");

include("app/models/Dbc.php");
include("app/models/Teacher.php");

// make sure this is the master
if (! (isMaster())) { 
  exitAccessError();
}

$db = Dbc::getReader();
$teacher = Teacher::fromEmail($db,$_POST['email']);
$new = $teacher->isFresh();

print '<article class="page">'."\n";
if ($new)
  print "<h1>New Teacher</h1>\n";
else
  print "<h1>Update Existing Teacher</h1>\n";

// Set all relevant variables
$teacher = Teacher::fresh();
$teacher->firstName = $_POST['firstName'];
$teacher->lastName = $_POST['lastName'];
$teacher->email = $_POST['email'];
$teacher->position = $_POST['position'];
$teacher->status = $_POST['status'];

$teacher->printSummary();

$db = Dbc::getReader();
if ($new)
  $teacher->addToDb($db);
else
  $teacher->updateDb($db);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
