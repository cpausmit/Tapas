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
  print '<p> You are the master:<br>'."\n";
  print "&nbsp;&nbsp; evaluations:";
  print '&nbsp;&nbsp; <a href="/evaluations">all</a>'."\n";
  print '&nbsp;&nbsp; <a href="/showActiveEvaluations">active</a>'."\n";
  print '&nbsp;&nbsp; <a href="/showEvaluators">evaluators</a>'."\n";
  print '&nbsp;&nbsp; <a href="/addEvaluation">evaluate</a>'."\n";
  print "<br>\n";
  print "&nbsp;&nbsp; preferences:";
  print '&nbsp;&nbsp; <a href="/showPreferences">preferences</a>'."\n";
  print "<br>\n";
  print "&nbsp;&nbsp; assignments:";
  print "<br>\n";
  print "&nbsp;&nbsp; course resources:";
  print '&nbsp;&nbsp; <a href="/planCourseResources">planning</a>'."\n";
  print "<br>\n";
  print "&nbsp;&nbsp; TAs:";
  print "<br>\n";
  print "&nbsp;&nbsp; access:";
  print '&nbsp;&nbsp; <a href="/showAccessLists">access lists</a>'."\n";
  print ' '."\n";
}
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
