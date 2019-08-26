<?php

// make sure access is allowed
include("app/views/admin/header.php");
if (! isMaster()) { 
  exitAccessError();
}

include_once("app/models/Preference.php");
include_once("app/models/Student.php");
include_once("app/models/Tables.php");

// read complete students table
$empty = true;
$students = Students::fromDb();

// find active preference table
$activeTables = new Tables('ActiveTables');
$term = substr($activeTables->getUniqueMatchingName('Preferences'),-5,5);
// do the query
$preferences = Preferences::fromDb($term);
$nTas = sizeof($preferences->list);

// start page
print '<article class="page">'."\n";
print '<h1>All Active TA Preferences</h1>'."\n";
print "<p>Number of TAs with registered preferences: $nTas</p>\n";
print "\n";

// print table header
print "<table>\n";
print "<tr>";
print "<th>&nbsp; Name &nbsp;</th>";
print "<th>&nbsp; Email &nbsp;</th>";
print "<th>&nbsp; Division &nbsp;</th>";
print "<th>&nbsp; Preference 1 &nbsp;</th>";
print "<th>&nbsp; Preference 2 &nbsp;</th>";
print "<th>&nbsp; Preference 3 &nbsp;</th>";
print "</tr>\n";

$empty = true;
foreach ($preferences->list as $key => $preference) {
  $empty = false;
  if (array_key_exists($preference->email,$students->list)) {
    $student = $students->list[$preference->email];
    $name = "$student->lastName, $student->firstName";
  }
  else {
    $student = Student::fresh();
    $student->email = $preference->email;
    $student->lastName = "<b>NO NAME FOUND IN DATABASE - FIX IT!</b>";
  }

  $task1 = new TeachingTask($preference->pref1);
  $task2 = new TeachingTask($preference->pref2);
  $task3 = new TeachingTask($preference->pref3);

  $student->printTableRow(true);
  print "<td>&nbsp; $student->division &nbsp;</td>";
  print "<td>&nbsp; ";
  $task1->printTaskWithLink();
  print " &nbsp;</td>";
  print "<td>&nbsp; ";
  $task2->printTaskWithLink();
  print " &nbsp;</td>";
  print "<td>&nbsp; ";
  $task3->printTaskWithLink();
  print " &nbsp;</td>";
}
print "</table>\n";

if ($empty)
  print "<br> No preferences found in this term ($term).";
  
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
