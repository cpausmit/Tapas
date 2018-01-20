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
$students = Students::fromDb($db);
// find active Ta table
$activeTables = new Tables($db,"ActiveTables");
$term = substr($activeTables->getUniqueMatchingName('Tas')-5,5);
$tas = Tas::fromDb($db,$term);
$nTas = sizeof($tas->list);

print "<article class=\"page\">\n";
print "<h1>Show Active TAs</h1>\n";
print "<p>Active TA term: $term";
print " with $nTas entries.</p>";
print "<hr>\n";
print "<table>\n";

// loop through all active TAs
$first = true;
foreach ($tas->list as $key => $ta) {
  if (isset($students->list[$ta->email])) {
    $student = $students->list[$ta->email];
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
