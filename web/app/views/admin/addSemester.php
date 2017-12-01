<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

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

function printEmptyForm()
{
  print '<h1>Add Semester to the Database</h1>'."\n";
  print ' '."\n";
  print '<table>';
  print '<form  action="/addSemester" method="post">'."\n";
  print '<tr><td>';
  print '  Term:&nbsp;'."\n";
  print '</td><td>';
  print '  <input type="text" name="term"><br>'."\n";
  print '</td></tr>';
  print '<tr><td> ------ </td><td> </td></tr>';
  print '<tr><td></td><td>';
  print '<input type="submit" value="add term" />'."\n";
  print '</td></tr>';
  print '</form>'."\n";
  print '</table>';
}

//==================================================================================================
// M A I N
//==================================================================================================

// connect to our database
$db = Dbc::getReader();

// get a full list of available semesters
$semesters = getSemestersFromDb($db);

// pick up the term from the form
$term = '';
if (array_key_exists('term',$_REQUEST))
  $term = $_REQUEST['term'];

// start our main page
print '<article class="page">'."\n";

if ($term == '') {
  printEmptyForm();
}
else {
  printEmptyForm();
  if (isset($semesters[$term])) {
  }
  else {
    $semester = Semester::fresh();
    $semester->term=$term;
    $semester->addToDb($db);
    $semesters = getSemestersFromDb($db);
  }
}

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

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
