<?php

include("app/views/ta/header.php");

// make sure we have a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Course.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

function generateOptions($db,$assignmentTable,$courses)
{
  // generate the options for all teaching assignment

  $options = array();
  $sql = "select * from $assignmentTable order by Task";
  $results = $db->query($sql);
  foreach ($results as $key => $row) {
    $task = $row[0];
    $email = $row[1];
    $myTask = new TeachingTask($task);
    if ($myTask->isTa() && $myTask->getEffort() == 'full') {
      $number = $myTask->getCourse();
      $course = $courses->list[$number];
      $option = $myTask->getTaTask() . ' --> ' . $course->name;
      $find = array_search($option,$options);
      if (! $find)
        $options[$task] = $option;
    }
  }
  
  return $options;
}

// get the list of courses
$db = Dbc::getReader();
$courses = Courses::fromDb($db);

// get all TAs and the possible full time assignments
$planningTables = new Tables($db,'PlanningTables');
$tasTable = $planningTables->getUniqueMatchingName('Tas');
$assignmentTable = $planningTables->getUniqueMatchingName('Assignments');

// select all possible options for the full time TAs
$options = generateOptions($db,$assignmentTable,$courses);

// start the page
print '<article class="page">'."\n";
print '<h1>Select TA Preferences</h1>'."\n";
print '<p>Make your selection and submit it. Please, select 3 distinct preferences.'."\n";
print 'The letters "U" and "R" indicate mostly grading (Utility) and significant in'."\n";
print 'class interaction with students (Recitation).</p>'."\n";
print ' '."\n";

// start the form
print '<form class="ta" action="/register" method="post">'."\n";

// start the table
print '<p>'."\n";
print '<table>'."\n";

// First preference
print '<tr><td>First preference?</td><td><select class="ta" name="pref1">'."\n";
print '  <option value="">Select</option>'."\n";
foreach ($options as $key => $option) {
  print "<option value=\"" . $key . "\"> $option </option>";
}
print '</select></td></tr>'."\n";

// Second preference
print '<tr><td>Second preference?&nbsp;&nbsp;</td><td><select class="ta" name="pref2">'."\n";
print '  <option value="">Select</option>'."\n";
foreach ($options as $key => $option) {
  print "<option value=\"" . $key . "\"> $option </option>";
}
print '</select></td></tr>'."\n";

// Third preference
print '<tr><td>Third preference?</td><td><select class="ta" name="pref3">'."\n";
print '  <option value="">Select</option>'."\n";
foreach ($options as $key => $option) {
  print "<option value=\"" . $key . "\"> $option </option>";
}
print '</select></td></tr>'."\n";

// close the table
print '</table>'."\n";

// complete the form
print '<input class="home" type="submit" value="submit your selection" />'."\n";
print '</form>'."\n";
print '</p>'."\n";
print ' '."\n";
print '</article>'."\n";

include("app/views/ta/footer.php");

?>
