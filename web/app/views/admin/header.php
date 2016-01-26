<?php

include("app/views/header.php");

if (isAdmin() && !isMaster()) {
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

  print '<div style="font-family: arial, verdana, sans-serif; font-size: 20px" class="transbox">'."\n";
  print " <div id='cssmenu'>";
  print " <ul>";
  print "    <li class='active'><a href='/admin'><span class=\"fa fa-home\">Admin</span></a></li>";
  print "    <li class='has-sub'><a href='#'><span>Courses</span></a>";
  print "       <ul>";
  print "          <li><a href='/courses'><span>all</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span>Students</span></a>";
  print "       <ul>";
  print "          <li><a href='/students'><span>all</span></a></li>";
  print "          <li><a href='/showActiveTas'><span>active</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span>Assignments</span></a>";
  print "       <ul>";
  print "          <li><a href='/assignments'><span>all</span></a></li>";
  print "          <li><a href='/showActiveAssignments'><span>active</span></a></li>";
  print "          <li><a href='/showLastAssignments'><span>last</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print " </ul>";
  print " </div>";
  print "</div>"."\n";
}

if (isMaster()) {

  print '<div style="font-family: arial, verdana, sans-serif; font-size: 20px" class="transbox">'."\n";
  print " <div id='cssmenu'>";
  print " <ul>";
  print "    <li class='active'><a href='/admin'><span class=\"fa fa-home\">Master</span></a></li>";
  print "    <li class='has-sub'><a href='#'><span>Courses</span></a>";
  print "       <ul>";
  print "          <li><a href='/courses'><span>all</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span>Students</span></a>";
  print "       <ul>";
  print "          <li><a href='/students'><span>all</span></a></li>";
  print "          <li><a href='/showActiveTas'><span>active</span></a></li>";
  print "          <li><a href='/showPreferences'><span>preferences</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span>Assignments</span></a>";
  print "       <ul>";
  print "          <li><a href='/assignments'><span>all</span></a></li>";
  print "          <li><a href='/showActiveAssignments'><span>active</span></a></li>";
  print "          <li><a href='/showLastAssignments'><span>last</span></a></li>";
  print "          <li><a href='/copyLastAssignments'><span>copy-last</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span>Evaluations</span></a>";
  print "       <ul>";
  print "          <li><a href='/evaluations'><span>all</span></a></li>";
  print "          <li><a href='/showActiveEvaluations'><span>active</span></a></li>";
  print "          <li><a href='/addEvaluation'><span>evaluate</span></a></li>";
  print "          <li><a href='/showEvaluators'><span>evaluators</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span>Misc</span></a>";
  print "       <ul>";
  print "          <li><a href='/showAccessLists'><span>access lists</span></a></li>";
  print "          <li><a href='/findTas'><span>find Tas</span></a></li>";
  print "          <li><a href='/email'><span>email</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print " </ul>";
  print " </div>";
  print "</div>"."\n";
}
?>
