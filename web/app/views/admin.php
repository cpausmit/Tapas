<?php

include("app/views/admin/header.php");

print '<article class="page">'."\n";
print '<h1>Administrative Functions</h1>'."\n";

if (isAdmin() || isMaster()) {
  print '<p> You have admin rights:<br>'."\n";
  print '&nbsp;&nbsp; <a href="/courses">courses</a>'."\n";
  print '&nbsp;&nbsp; <a href="/students">students</a>'."\n";
  print '&nbsp;&nbsp; <a href="/assignments">assignments</a>'."\n";
  print '&nbsp;&nbsp; <a href="/showActiveAssignments?option=TA">active Assignments</a>'."\n";
  print '&nbsp;&nbsp; <a href="/showActiveTas">active Tas</a>'."\n";
  print ' '."\n";
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
