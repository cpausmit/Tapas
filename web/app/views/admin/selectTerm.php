<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Semester.php");
include_once("app/models/Tables.php");

//==================================================================================================
// M A I N
//==================================================================================================

// find the active table
$activeTables = new Tables("ActiveTables");
// get a full list of available semesters
$semesters = Semesters::fromDb();

print "<article class=\"page\">\n";
//print "<h1>POST</h1>\n";
//print "<p><ul style=\"list-style-type:disc\">\n";
$update = False;
foreach($_POST as $key => $value) {
    //print "  <li> $key --> $value </li>\n";
  $activeTables->updateMatching($key,$value); 
  $update = True;
}
//print "</ul></p>\n";
if ($update) {
  $activeTables->readTable();
}

print '<h1>Term Selection</h1>'."\n";
print ' '."\n";
print "Select the active term for each activity<br>\n";
print '<p>';
print '<form action="/selectTerm" method="post">'."\n";
foreach ($activeTables->getNames() as $key => $name) {
  $t = substr($name,-5,5);
  $a = substr($name,0,strlen($name)-5);
  print "$a: ";
  print '  <select class="'.$a.'" name="'.$a.'">'."\n";
  print '  <option value="'.$t.'">'.$t.'</option>'."\n";
  foreach ($semesters->list as $k => $semester)
    print "<option value=\"" . $k . "\"> $k </option>";
  print '  </select>'."\n";
}

print '  <input type="submit" value="select these active terms" />'."\n";
print '</form>'."\n";
print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
