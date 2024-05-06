<?php

include("app/views/ta/header.php");

// make sure we have a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");
include_once("app/models/Course.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

function generateOptions($term,$courses)
{
  // generate the options for all teaching assignment

  $options = array();
  $assignments = Assignments::fromDb($term);
  foreach ($assignments->list as $key => $assignment) {
    $myTask = new TeachingTask($assignment->task);
    if ($myTask->isTa() && $myTask->getEffort() != 'part') {
      $number = $myTask->getCourse();
      $course = $courses->list[$number];
      $option = $myTask->getTaTask() . ' --> ' . $course->name;
      $find = array_search($option,$options);
      if (! $find)
        $options[$assignment->task] = $option;
    }
  }
  
  return $options;
}

// get the list of courses
$courses = Courses::fromDb();

// get all TAs and the possible full time assignments
$planningTables = new Tables('PlanningTables');
$tasTable = $planningTables->getUniqueMatchingName('Tas');
$term = substr($planningTables->getUniqueMatchingName('Assignments'),-5,5);

// print 'TERM: '.$term;

// select all possible options for the full time TAs
$options = generateOptions($term,$courses);

// start the page
print '<article class="page">'."\n";
print "<h1>Select TA Preferences (Term: $term)</h1>\n";
print '<p>Make your selection and submit it. Please, select 3 distinct preferences.'."\n";
print 'The letters "U" and "R" indicate mostly grading (Utility) and significant in'."\n";
print 'class interaction with students (Recitation).</p>'."\n";
print ' '."\n";

// start the form
print '<form class="ta" action="/register" method="post">'."\n";

// start the table
print '<p>'."\n";
print '<table cellborder="1">'."\n";
    
if (isMaster()) {
  print '<tr><td>';
  print '  TA email:&nbsp;'."\n";
  print '</td><td>';
  print '  <input type="text" name="email"><br>'."\n";
  print '</td></tr>';
}
else {
  print '';
}

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

print '<tr><td></td><td>Comments:</td></tr>';

print '<tr><td></td><td>'
    . '<textarea  style="font-family: arial, verdana, sans-serif; font-size: 20px;'
    . ' color: black; background-color: white" name="Comment" rows=1 cols=60></textarea>';
print "</td></tr>";

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
