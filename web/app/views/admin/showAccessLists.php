<?php

// global variable to manage the result of the form input
$GLOBALS['DB_CREATIONS'] = 0;

include("app/views/admin/header.php");

include_once("app/models/Admin.php");
include_once("app/models/Ta.php");
include_once("app/models/Tables.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

// get the active tables to consider
$activeTables = new Tables(Dbc::getReader(),'ActiveTables');
$taTerm = substr($activeTables->getUniqueMatchingName('Tas'),-5,5);
$teacherTerm = substr($activeTables->getUniqueMatchingName('Assignments'),-5,5);

// get admins, tas and teachers from database
$admins = Admins::fromDb(Dbc::getReader());
$tas = Tas::fromDb(Dbc::getReader(),$taTerm);
$teachers = Tas::fromDb(Dbc::getReader(),$teacherTerm);

print '<article class="page">'."\n";
print "<h1>Admins</h1><p>";
$admins->printTable();
print "<h1>TAs</h1><p>";
$tas->printTable();
print "<h1>Teachers</h1><p>";
$teachers->printTable();

print " Number of DB creations: ".$GLOBALS['DB_CREATIONS']."<br>\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
