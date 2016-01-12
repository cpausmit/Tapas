<?php

// make sure we have an admin
include("app/views/admin/header.php");
if (! (isAdmin() || isMaster())) {
  exitAccessError();
}

// load database and models
include("app/models/Dbc.php");
include("app/models/Student.php");

// create an instance
$db = Dbc::getReader();
$students = $db->query("select * from Students order by lastName");

// print basic page
print '<article class="page">'."\n";
print "<hr>\n";
print "<h1>Graduate Student TA Listing</h1>\n";
print "<hr>\n";
print "<table>\n";

// loop
$first = true;
foreach ($students as $key => $row) {
  $student = Student::fromRow($row);
  $student->printTableRow($first);
  $first = false;
}

// footer
print "</table>\n";
print "<hr>\n";
print "</article>\n";

include("app/views/admin/footer.php");

?>
