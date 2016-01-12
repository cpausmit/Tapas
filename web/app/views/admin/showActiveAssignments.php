<?php

include("app/views/admin/header.php");
include("app/models/TeachingTask.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Show Active TA Assignments</h1>';
print ' ';

// connect to our database
$link = getLink();

// find the active tables and the last non-active table
$active = findActiveTable($link,'Assignments');
print "Active assignment table: $active[0]</p>";

// show last assignments
showAssignment($link,$active[0]);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
