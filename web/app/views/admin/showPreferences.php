<?php

// make sure access is allowed
include("app/views/admin/header.php");
if (! isMaster()) { 
  exitAccessError();
}

include("app/models/Dbc.php");
include("app/models/ActiveTables.php");
include("app/models/Student.php");
include("app/models/Preference.php");

// connect to our database
$db = Dbc::getReader();

// read complete students table
$empty = true;
$students = "";
$rows = $db->query("select * from Students order by lastName");
foreach ($rows as $key => $row) {
  $student = Student::fromRow($row);
  $students[$student->email] = $student;
}


// find active preference table
$activeTables = new ActiveTables($db);
$preferenceTable = $activeTables->getUniqueMatchingName('Preferences');
// do the query
$nTas = 0;
$preferences = "";
$rows = $db->query("select * from $preferenceTable");
foreach ($rows as $key => $row) {
  $preference = Preference::fromRow($row);
  $preferences[$preference->email] = $preference;
  $nTas = $nTas + 1;
}

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
print "<th>&nbsp; Preference 1 &nbsp;</th>";
print "<th>&nbsp; Preference 2 &nbsp;</th>";
print "<th>&nbsp; Preference 3 &nbsp;</th>";
print "</tr>\n";

$empty = true;
foreach ($preferences as $email => $preference) {
  $empty = false;
  if (array_key_exists($email,$students)) {
    $student = $students[$email];
    $name = "$student->lastName, $student->firstName";
  }
  else
    $name = "<b>NO NAME FOUND IN DATABASE - FIX IT!</b>";
  $task1 = new TeachingTask($preference->pref1);
  $task2 = new TeachingTask($preference->pref2);
  $task3 = new TeachingTask($preference->pref3);

  $student->printTableRow(true);
  //print "<td>&nbsp; ".$task1->getTaTask()." &nbsp;</td>";
  //print "<td>&nbsp; ".$task2->getTaTask()." &nbsp;</td>";
  //print "<td>&nbsp; ".$task3->getTaTask()." &nbsp;</td>";
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
  print ' No preferences found in this term.';
  
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
