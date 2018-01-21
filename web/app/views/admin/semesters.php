<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) {
  exitAccessError();
}

include_once("app/models/Semester.php");

//==================================================================================================
// M A I N
//==================================================================================================

// get a full list of available semesters
$semesters = Semesters::fromDb();

print '<article class="page">';
print '<hr>';
print '<h1>Available Semesters</h1>';
print '<hr>';
print '<p>';
print '<table>';
print "<tr><th> Semester ID </th></tr>";
foreach ($semesters->list as $key => $semester)
  print "<tr><td> $key </td></tr>";
print '</table>';
print '<hr>';
print '</article>';

include("app/views/admin/footer.php");

?>
