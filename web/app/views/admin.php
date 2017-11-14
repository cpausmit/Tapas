<?php

include("app/views/admin/header.php");

print '<article class="page">'."\n";
print '<h1>Administrative Functions</h1>'."\n";

if (isAdmin() || isMaster()) {

  print '<p> Administrators can list:</p>'."\n";
  print '<ul class="cp-list">'."\n";
  print '<li> <a href="/courses">courses</a>'." all courses in the physics department.\n";
  print '<li> <a href="/students">students</a>'." all grad students that have been a TA\n";
  print '<li> <a href="/showActiveAssignments?option=TA">active assignments</a>'." list\n";
  print "     active assignments in the system.\n";
  print '<li> <a href="/showActiveTas">active TAs</a>'." list active TAs in the system.\n";
  print '<li> <a href="/planTas">planning TAs</a>'." list TAs for next term planning.\n";
  print "</ul>\n";
  print ' '."\n";
  print ' <hr>'."\n";
  print ' Thank you! for the input, this is very useful for future assignments.';

//  print '<p> You have admin rights:<br>'."\n";
//  print '&nbsp;&nbsp; <a href="/courses">courses</a>'."\n";
//  print '&nbsp;&nbsp; <a href="/students">students</a>'."\n";
//  print '&nbsp;&nbsp; <a href="/assignments">assignments</a>'."\n";
//  print '&nbsp;&nbsp; <a href="/showActiveAssignments?option=TA">active Assignments</a>'."\n";
//  print '&nbsp;&nbsp; <a href="/showActiveTas">active TAs</a>'."\n";
//  print '&nbsp;&nbsp; <a href="/planTas">TA planning</a>'."\n";
//  print ' '."\n";
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
  print '&nbsp;&nbsp; <a href="/showLastAssignments">show last</a>'."\n";
  print '&nbsp;&nbsp; <a href="/copyLastAssignments">copy last</a>'."\n";
  print "<br>\n";
  print "&nbsp;&nbsp; TAs:";
  print '&nbsp;&nbsp; <a href="/findTas">find Tas</a>'."\n";
  print "<br>\n";
  print "&nbsp;&nbsp; access:";
  print '&nbsp;&nbsp; <a href="/showAccessLists">access lists</a>'."\n";
  print ' '."\n";
}
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
