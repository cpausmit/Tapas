<?php

include("app/views/teacher/header.php");
include_once("app/models/TeachingTask.php");

// make sure we have a registered TA
if (! (isTeacher() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Select the TA</h1>'."\n";
print ' '."\n";

// connect to our database
$link = getLink();
// find the active table
$active = findActiveTable($link,'Evaluations');
print "Active evaluations table: $active[0]</p>";

$term = substr($active[0],-5,5);
$options = findTaNames($link,'Assignments'.$term);

// start the form
print '<p>';
print '<form action="/enterEvaluation" method="post">'."\n";
// Select TA
print 'Select the student to evaluate.';
print '  <select class="ta" name="ta">'."\n";
print '  <option value="">Select student to evaluate</option>'."\n";
foreach ($options as $key => $option) {
  print "<option value=\"" . $key . "\"> $option </option>";
}
print '  </select>'."\n";
print '  <input type="submit" value="select this student" />'."\n";
print '</form>'."\n";
print '</p>'."\n";

print '</article>'."\n";

include("app/views/teacher/footer.php");

?>
