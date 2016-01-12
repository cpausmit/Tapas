<?php

include("app/views/admin/header.php");
include("app/models/TeachingTask.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Show last years TA Assignments</h1>';
print ' ';

// connect to our database
$link = getLink();

// find the active tables and the last non-active table
$active = findActiveTable($link,'Assignments');
$pattern = substr($active[0],0,-4);
$last = findLastTable($link,$pattern,$active[0]);
print "Last assignment table: $last</p>";

// show last assignments
showAssignment($link,$last);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
