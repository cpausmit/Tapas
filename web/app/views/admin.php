<?php

include("app/views/admin/header.php");

print '<article class="page">'."\n";
print '<h1>Administrative Functions</h1>'."\n";

if (isAdmin() || isMaster()) {

  print '<p> Administrators can list:</p>'."\n";
  print '<ul class="cp-list">'."\n";
  print '<li> <a href="/semesters">semesters</a>'." all semesters so far recorded.\n";
  print '<li> <a href="/selectTerm">active semesters</a>'." active semesters per activity.\n";
  print '<li> <a href="/courses">courses</a>'." all courses in the physics department.\n";
  print '<li> <a href="/teachers">teachers</a>'." all teachers (mostly faculty).\n";
  print '<li> <a href="/students">students</a>'." all grad students that have been a TA.\n";
  print '<li> <a href="/showActiveTas">active TAs</a>'." active TAs in the system.\n";
  print '<li> <a href="/planTas">planning TAs</a>'." TAs for next term planning.\n";
  print '<li> <a href="/showActiveAssignments?option=TA">active TA assignments</a>'."\n";
  print "     active TA assignments in the system.\n";
  print '<li> <a href="/showActiveAssignments?option=ALL">all active assignments</a>'."\n";
  print "     all active assignments in the system.\n";
  print '<li> <a href="/planCourseResources">plan course resources</a>'."\n";
  print "     for the next term.\n";
  print "</ul>\n";
  print ' '."\n";
  print ' <hr>'."\n";
}

if (isMaster()) {
  print '<p> Additional master menu:<br>'."\n";
  print '<ul class="cp-list">'."\n";
  print '<li> evaluations: <a href="/showActiveEvaluations">show active</a>, <a href="/showEvaluators">active evaluators</a>, <a href="/evaluations">show any</a>, <a href="/addEvaluation">add evaluation</a>'."\n";
  print '<li> preferences: <a href="/showPreferences">show active</a>'."\n";
  print '<li> access list: <a href="/showAccessLists">show</a>'."\n";
  print "</ul>\n";
  print ' '."\n";
  print ' <hr>'."\n";
}
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
