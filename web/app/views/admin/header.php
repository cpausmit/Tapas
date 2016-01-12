<?php

include("app/views/header.php");

if (isAdmin() || isMaster()) {
  print '<div style="font-family: arial, verdana, sans-serif; font-size: 20px; color: darkred; background-color: pink" class="transbox">'."\n";
  print '&nbsp; Admins:'."\n";
  print '&nbsp; <a href="/admin"> <span class="fa fa-home">Home</span></a> &nbsp;'."\n";
  print ' <a href="/courses">courses</a>'."\n";
  print ' students:';
  print ' <a href="/students">all</a>'."\n";
  print ' <a href="/showActiveTas">active</a>'."\n";
  print ' assignments:';
  print ' <a href="/assignments">all</a>'."\n";
  print ' <a href="/showActiveAssignments">active</a>'."\n";
  print '</div>'."\n";
}
else {
  print ' You are no admin.<br>';
}

if (isMaster()) {
  print '<div style="font-family: arial, verdana, sans-serif; font-size: 20px; color: darkgreen; background-color: mintgreen" class="transbox">'."\n";
  print '&nbsp; Master:'."\n";
  print ' &nbsp; evaluations:';
  print ' <a href="/evaluations">all</a>'."\n";
  print ' <a href="/showActiveEvaluations">active</a>'."\n";
  print ' <a href="/showEvaluators">evaluators</a>'."\n";
  print ' <a href="/addEvaluation">evaluate</a>'."\n";
  print ' &nbsp; misc:';
  print ' <a href="/showPreferences">preferences</a>'."\n";
  print ' <a href="/showAccessLists">access</a>'."\n";
  print ' <a href="/showLastAssignments">lastA</a>'."\n";
  print ' <a href="/copyLastAssignments">copyL</a>'."\n";
  print ' <a href="/findTas">findTas</a>'."\n";
  print ' <a href="/email">email</a>'."\n";
  print '</div>'."\n";
}
?>
