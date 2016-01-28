<?php

include("app/views/admin/header.php");
include("app/models/TeachingTask.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

// command line arguments
$option = $_GET['option'];  // this is the input course number

print '<article class="page">'."\n";
print '<h1>Show Active Assignments</h1>';
print ' ';

// connect to our database
$link = getLink();

// find the active tables and the last non-active table
$active = findActiveTable($link,'Assignments');

// show unassigned slots
print "Unassigned slots from $active[0]</p>";
showAssignment($link,$active[0],"Unassigned");

// show all TA slots
print "Active Assignments from $active[0]</p>";
showAssignment($link,$active[0],$option);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
