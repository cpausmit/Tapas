<?php

include("app/views/ta/header.php");
include("app/models/Dbc.php");
include("app/models/TeachingTask.php");

// make sure we have a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

//// get the list of courses
$db = Dbc::getReader();
$courses = "";
$courseRows = $db->query("select * from Courses order by Number");
foreach ($courseRows as $key => $row) {
  $course = Course::fromRow($row);
  $courses[$course->number] = $course;
  print ' number>' . $course->number . '<';
 }

print '<article class="page">'."\n";
print '<h1>Select TA Preferences</h1>'."\n";
print '<p>Make your selection and submit it. Please, select 3 distinct preferences.'."\n";
print 'The letters "U" and "R" indicate mostly grading (Utility) and significant in'."\n";
print 'class interaction with students (Recitation).</p>'."\n";
print ' '."\n";

// connect to our database
$link = getLink();

// find the active TA table
$active = findActiveTable($link,'Tas');
$tasTable =  $active[0];

// find the active Assignment table
$active = findActiveTable($link,'Assignments');
print "Active tables: $tasTable (TAs), $active[0] (Assignments)</p>";

// start the form
print '<form class="ta" action="/register" method="post">'."\n";

// start the table
print '<p>'."\n";
print '<table>'."\n";

// generate the options
$options = array();
$query = "select * from $active[0] order by Task";
$statement = $link->prepare($query);
$statement->execute();
$statement->bind_result($task,$email);
while ($statement->fetch()) {
  $myTask = new TeachingTask($task);
  if ($myTask->isTa() && $myTask->getEffort() == 'full') {
    $option = $myTask->getTaTask();
    $number = $myTask->getCourse();
    $course = $courses[$number];
    $find = array_search($option,$options);
    if (! $find) {
      //$options[$task] = $option;
      $options[$task] = $option . ' --> ' . $course->name;
    }
  }
}

// First preference
print '<tr><td>What is your first preference?</td><td><select class="ta" name="pref1">'."\n";
print '  <option value="">Select Preference 1</option>'."\n";
foreach ($options as $key => $option) {
  print "<option value=\"" . $key . "\"> $option </option>";
}
print '</select></td></tr>'."\n";

// Second preference
$query = "select * from $active[0]";
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
