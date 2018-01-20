<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

include_once("app/models/Dbc.php");
include_once("app/models/Semester.php");
include_once("app/models/Utils.php");

print '<article class="page">'."\n";
print '<h1>Show All TA Assignments</h1>';
print ' ';

// get a full list of available semesters
$semesters = Semesters::fromDb(Dbc::getReader());

print "<table>\n";
print "<tr><th>Term</th><th>Actions</th>\n";
foreach ($semesters->list as $term => $semester) {
  print " <tr><td>&nbsp; $term\n";
  print " </td><td>&nbsp; <a href=\"/showAssignments?term=$term\">show</a></td></tr>\n";
}
print "</table>\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
