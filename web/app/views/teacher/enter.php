<?php

include("app/views/teacher/header.php");

// make sure we have a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");
include_once("app/models/Student.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

function getNamesFromDb($term)
{
  $students = Students::fromDb();
  $assignments = Assignments::fromDb($term);

  // loop through all assignments and enter the names of TAs to be evaluated
  $names = array();
  foreach ($assignments->list as $key => $assignment) {
    if (isset($students->list[$assignment->person])) {
      $student = $students->list[$assignment->person];
      $myTask = new TeachingTask($assignment->task);
      $names[$assignment->person] = $student->lastName . ', ' . $student->firstName . '  Task: ' . $myTask->getTaTask();
    }
  }

  // make sure to sort the names alphabetically
  asort($names);

  return $names;
}

//==================================================================================================
// M A I N
//==================================================================================================

// find the active table
$activeTables = new Tables("ActiveTables");
$term = substr($activeTables->getUniqueMatchingName('Evaluations'),-5,5);
$names = getNamesFromDb($term);

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
