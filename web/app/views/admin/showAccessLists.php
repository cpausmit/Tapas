<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

include_once("app/models/Admin.php");
include_once("app/models/Ta.php");
include_once("app/models/Teacher.php");
include_once("app/models/Tables.php");


// get the active tables to consider
$activeTables = new Tables('ActiveTables');
$taTerm = substr($activeTables->getUniqueMatchingName('Tas'),-5,5);
$teacherTerm = substr($activeTables->getUniqueMatchingName('Assignments'),-5,5);

// get admins, tas and teachers from database
$admins = Admins::fromDb();
$tas = Tas::fromDb($taTerm);
$teachers = Teachers::fromDb($teacherTerm);

print '<article class="page">'."\n";
print "<h1>Admins</h1><p>";
$admins->printTable();
print "<h1>TAs</h1><p>";
$tas->printTable();
print "<h1>Teachers</h1><p>";
$teachers->printTable();

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
