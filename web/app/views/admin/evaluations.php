<?php

// test access rights
include("app/views/admin/header.php");
if (! isMaster()) { 
  exitAccessError();
}

include_once("app/models/Semester.php");

// find the active tables and the last non active table
$semesters = Semesters::fromDb();

print '<article class="page">'."\n";
print '<h1>Show All TA Evaluations</h1>';
print ' ';

print "<table>\n";
print "<tr><th>Term</th><th>Actions</th>\n";
foreach ($semesters->list as $key => $semester) {
  $term = $semester->term;
  print " <tr><td>&nbsp; $term\n";
  print " </td><td>&nbsp; <a href=\"/showEvaluations?term=$term\">show</a></td></tr>\n";
}
print '</table>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
