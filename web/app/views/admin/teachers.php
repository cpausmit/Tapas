<?php

// make sure we have an admin
include("app/views/admin/header.php");
if (! (isAdmin() || isMaster())) {
  exitAccessError();
}

// load database and models
include("app/models/Dbc.php");
include("app/models/Teacher.php");

// create an instance
$db = Dbc::getReader();
$teachers = $db->query("select * from Teachers order by lastName");

// print basic page
print '<article class="page">'."\n";

print "<hr>\n";
print "<h1>Teachers Listing (Faculty)</h1>\n";
print "<hr>\n";
print "<table>\n";

// loop
$first = true;
foreach ($teachers as $key => $row) {
  $teacher = Teacher::fromRow($row);
  $teacher->printTableRow($first);
  $first = false;
}

// footer
print "</table>\n";
print "<hr>\n";

print "</article>\n";

include("app/views/admin/footer.php");

?>
