<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) {
  exitAccessError();
}

include("app/models/Semester.php");

// Get all semesters from the database
function getSemestersFromDb($db)
{
  // read complete courses table
  $semesters = "";
  $rows = $db->query("select * from Semesters order by Term");
  foreach ($rows as $key => $row) {
    $semester = Semester::fromRow($row);
    $semesters[$semester->term] = $semester;
  }

  return $semesters;
}

//==================================================================================================
// M A I N
//==================================================================================================

// connect to our database
$db = Dbc::getReader();

// get a full list of available semesters
$semesters = getSemestersFromDb($db);

print '<article class="page">';
print '<hr>';
print '<h1>Available Semesters</h1>';
print '<hr>';
print '<p>';
print '<table>';
print "<tr><th> Semester ID </th></tr>";
foreach ($semesters as $key => $semester)
  print "<tr><td> $key </td></tr>";
print '</table>';
print '<hr>';
print '</article>';

include("app/views/admin/footer.php");

?>
