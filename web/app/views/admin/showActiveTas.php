<?php

// make sure we are dealing with a registered TA
include("app/views/admin/header.php");
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Dbc.php");
include_once("app/models/Tables.php");
include_once("app/models/Student.php");
include_once("app/models/Ta.php");

// connect to our database
$db = Dbc::getReader();

// read complete students table
$students = "";
$rows = $db->query("select * from Students order by lastName");
foreach ($rows as $key => $row) {
  $student = Student::fromRow($row);
  $students[$student->email] = $student;
}

// find active Ta table
$activeTables = new Tables($db,"ActiveTables");
$taTable = $activeTables->getUniqueMatchingName('Tas');
// do the query
$rows = $db->query("select Email, Fulltime, PartTime from $taTable order by Email");
$i = 0;
$tas = "";
foreach ($rows as $key => $row) {
  $ta = Ta::fromRow($row);
  $tas[$i] = $ta;
  $i = $i + 1;
}
$nTas = $i;

print "<article class=\"page\">\n";
print "<h1>Show Active TAs</h1>\n";
print "<p>Active TA table: $taTable";
print " with $nTas entries.</p>";
print "<hr>\n";
print "<table>\n";

// loop through all active TAs
$first = true;
foreach ($tas as $key => $ta) {
  if (isset($students[$ta->email])) {
    $student = $students[$ta->email];
    if ($first) {
      $student->printTableHeader(true);
      print "<th>&nbsp; FullTime &nbsp;</th><th>&nbsp; PartTime &nbsp;</th></tr>";
      $first = false;
    }
    $student->printTableRow(true);
    print "<td align=center>&nbsp;$ta->fullTime</td><td align=center>&nbsp;$ta->partTime</td></tr>";
  }
  else {
    print "<br><b> ERROR -- student not found in database: $ta->email</b><br>";
  }
}

//footer
print "</table>\n";
print "<hr>\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
