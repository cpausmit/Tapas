<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Assignment.php");
include_once("app/models/Tables.php");

// command line arguments
$option = $_GET['option'];  // this is the input course number

// find the active tables and the last non-active table
$activeTables = new Tables("ActiveTables");
$assignmentsTable = $activeTables->getUniqueMatchingName('Assignments');
$term = substr($assignmentsTable,-5,5);
$assignments = Assignments::fromDb($term);

print '<article class="page">'."\n";
print '<h1>Show Active Assignments</h1>';
print ' ';
print "Unassigned slots from $assignmentsTable</p>";
$assignments->show("Unassigned");
print "Active Assignments from $assignmentsTable</p>";
$assignments->show($option);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
