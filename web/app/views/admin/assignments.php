<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

print '<article class="page">'."\n";
print '<h1>Show All TA Assignments</h1>';
print ' ';

// find the active tables and the last non active table
$tables = getTables(Dbc::GetReader(),'Assignments_____');

print "<table>\n";
print "<tr><th>Term</th><th>Actions</th>\n";
foreach ($tables as $key => $table) {
  $term = substr($table,-5,5);
  print " <tr><td>&nbsp; $term\n";
  print " </td><td>&nbsp; <a href=\"/showAssignments?term=$term\">show</a></td></tr>\n";
}
print "</table>\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
