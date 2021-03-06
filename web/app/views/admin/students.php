<?php

// make sure we have an admin
include("app/views/admin/header.php");

if (! (isAdmin() || isMaster())) {
  exitAccessError();
}

// load database and models
include_once("app/models/Student.php");

// create an instance
$students = Students::fromDb();

print '<article class="page">'."\n";
print "<hr>\n";
print "<h1>Graduate Student TA Listing</h1>\n";
print "<hr>\n";
print "<table>\n";

// loop
$first = true;
foreach ($students->list as $key => $student) {
  $student->printTableRow($first);
  $first = false;
}

print "</table>\n";
print "<hr>\n";

print "</article>\n";

include("app/views/admin/footer.php");

?>
