<?php

include("app/views/admin/header.php");
include("app/models/TeachingTask.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

// command line arguments
$option = $_GET['option'];  // this is the input course number

// connect to our database
$link = getLink();

// find the active tables and the last non-active table
$activeTables = new Tables(Dbc::getReader(),"ActiveTables");
$assignmentsTable = $activeTables->getUniqueMatchingName('Assignments');

print '<article class="page">'."\n";
print '<h1>Show Active Assignments</h1>';
print ' ';

// show unassigned slots
print "Unassigned slots from $assignmentsTable</p>";
showAssignment($link,$assignmentTable,"Unassigned");

// show all TA slots
print "Active Assignments from $active[0]</p>";
showAssignment($link,$assignmentTable,$option);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
