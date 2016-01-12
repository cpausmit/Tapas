<?php

  // test access rights
include("app/views/admin/header.php");
if (! isMaster()) { 
  exitAccessError();
}

// connect to our database
$link = getLink();

// find the active tables and the last non active table
$tables = findTables($link,'Evaluations');

print '<article class="page">'."\n";
print '<h1>Show All TA Evaluations</h1>';
print ' ';

print "<table>\n";
print "<tr><th>Term</th><th>Actions</th>\n";
foreach ($tables as $key => $table) {
  $term = substr($table,-5,5);
  print " <tr><td>&nbsp; $term\n";
  print " </td><td>&nbsp; <a href=\"/showEvaluations?term=$term\">show</a></td></tr>\n";
}
print '</table>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
