<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

$link = getLink();
$active = findActiveTable($link,'Evaluations');
$term = substr($active[0],-5,5);
// get all course numbers with full time TA
$courses = findCourseNumbers($link,'Assignments'.$term);


print '<article class="page">'."\n";

// find the active evaluation table
print "Active evaluations table: $active[0]</p>";
printTeachers($link,'Assignments'.$term,$courses);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
