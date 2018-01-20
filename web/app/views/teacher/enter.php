<?php

include("app/views/teacher/header.php");

// make sure we have a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Dbc.php");
include_once("app/models/Semester.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

function getNamesFromDb($db,$term)
{
  $sql = "select Students.FirstName, Students.LastName, Assignments.Person, Assignments.Task"
      . " from Assignments,Students "
      . " where Assignments.Term = 'Assignmentserm' and Students.Email = Assignments.Person"
      . " order by Students.LastName";
  $taskRows = $db->query($sql);

  $names = "";
  foreach ($taskRows as $key => $row) {
    $myTask = new TeachingTask($row[3]);
    $names[$row[2]] = $row[1] . ', ' . $row[0] . '  Task: ' . $myTask->getTaTask();
  }

  return $names;
}

//==================================================================================================
// M A I N
//==================================================================================================

// connect to our database
$db = Dbc::getReader();

// get a full list of available semesters
$semesters = Semesters::fromDb($db);

// find the active table
$activeTables = new Tables($db,"ActiveTables");
$evaluationsTable = $activeTables->getUniqueMatchingName('Evaluations');
$term = substr($evaluationsTable,-5,5);
$names = getNamesFromDb($db,$term);

print '<article class="page">'."\n";

print '<h1>Select the TA</h1>'."\n";
print ' '."\n";
print "Active evaluations term: $term (assignments)<br>\n";

print '<p>';
print '<form action="/enterEvaluation" method="post">'."\n";
print 'Select the student to evaluate.';
print '  <select class="ta" name="ta">'."\n";
print '  <option value="">Select student to evaluate</option>'."\n";
foreach ($names as $key => $name)
  print "<option value=\"" . $key . "\"> $name </option>";
print '  </select>'."\n";
print '  <input type="submit" value="select this student" />'."\n";
print '</form>'."\n";
print '</p>'."\n";

print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
