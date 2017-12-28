<?php

include("app/views/ta/header.php");

// make sure we have a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include("app/models/Dbc.php");
include("app/models/TeachingTask.php");
include("app/models/Course.php");
include("app/models/Tables.php");

function generateOptions($db,$assignmentTable,$courses)
{
  // generate the options

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
//print_r($courses);

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
print '<tr><td>What is your first preference?</td><td><select class="ta" name="pref1">'."\n";
print '  <option value="">Select Preference 1</option>'."\n";
foreach ($options as $key => $option) {
  print "<option value=\"" . $key . "\"> $option </option>";
}
print '</select></td></tr>'."\n";

// Second preference
print '<tr><td>What is your second preference?&nbsp;&nbsp;</td><td><select class="ta" name="pref2">'."\n";
print '  <option value="">Select Preference 2</option>'."\n";
foreach ($options as $key => $option) {
  print "<option value=\"" . $key . "\"> $option </option>";
}
print '</select></td></tr>'."\n";

// Third preference
print '<tr><td>What is your third preference?</td><td><select class="ta" name="pref3">'."\n";
print '  <option value="">Select Preference 3</option>'."\n";
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
