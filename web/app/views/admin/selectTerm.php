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

// find the active and planning table
$activeTables = new Tables("ActiveTables");
$planningTables = new Tables("PlanningTables");

// get a full list of available semesters
$semesters = Semesters::fromDb();

print "<article class=\"page\">\n";

if (isset($_GET['Tables'])) {
  $tables = $activeTables;
  if ($_GET['Tables'] == "Planning")
    $tables = $planningTables;
  $update = False;
  foreach($_POST as $key => $value) {
    $tables->updateMatching($key,$value); 
    $update = True;
  }
  if ($update)
    $tables->readTable();
}


print '<h1>Active terms</h1>'."\n";
print '<form action="/selectTerm?Tables=Active" method="post">'."\n";
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

print '<h1>Planning terms</h1>'."\n";
print '<form action="/selectTerm?Tables=Planning" method="post">'."\n";
foreach ($planningTables->getNames() as $key => $name) {
  $t = substr($name,-5,5);
  $a = substr($name,0,strlen($name)-5);
  print "$a: ";
  print '  <select class="'.$a.'" name="'.$a.'">'."\n";
  print '  <option value="'.$t.'">'.$t.'</option>'."\n";
  foreach ($semesters->list as $k => $semester)
    print "<option value=\"" . $k . "\"> $k </option>";
  print '  </select>'."\n";
}
print '  <input type="submit" value="select these planning terms" />'."\n";
print '</form>'."\n";
print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
