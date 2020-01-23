<?php

include("app/views/header.php");

if (isAdmin() && !isMaster()) {
  print '<div style="font-family: arial, verdana, sans-serif; font-size: 20px" class="transbox">'."\n";
  print " <div id='cssmenu'>";
  print " <ul>";
  print "    <li><span class=\"fa fa-home\"> Admin</span>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-cogs\"> Basics</span></a>";
  print "       <ul>";
  print "          <li><a href='/semesters'><span>Semesters</span></a></li>";
  print "          <li><a href='/courses'><span>Courses</span></a></li>";
  print "          <li><a href='/teachers'><span>Teachers</span></a></li>";
  print "          <li><a href='/students'><span>Students</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='active'><a href='/admin'><span class=\"fa fa-plus\"> Adding</span></a>";
  print "       <ul>";
  print "          <li><a href='/addSemester'><span>semester</span></a></li>";
  print "          <li><a href='/addCourse'><span>course</span></a></li>";
  print "          <li><a href='/addTeacher'><span>teacher</span></a></li>";
  print "          <li><a href='/addStudent'><span>student</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-group\"> TA Lists</span></a>";
  print "       <ul>";
  print "          <li><a href='/showActiveTas'><span>active</span></a></li>";
  print "          <li><a href='/planTas'><span>planning</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-magic\"> Active</span></a>";
  print "       <ul>";
  print "          <li><a href='/selectTerm'><span>semesters</span></a></li>";
  print "          <li><a href='/showActiveAssignments?option=ALL'><span>all</span></a></li>";
  print "          <li><a href='/showActiveAssignments?option=TA'><span>TAs</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-reorder\"> Resources</span></a>";
  print "       <ul>";
  print "          <li><a href='/planCourseResources'><span>planning</span></a></li>";
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
  print "    <li><span class=\"fa fa-home\"> Master</span>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-cogs\"> Basics</span></a>";
  print "       <ul>";
  print "          <li><a href='/semesters'><span>Semesters</span></a></li>";
  print "          <li><a href='/courses'><span>Courses</span></a></li>";
  print "          <li><a href='/teachers'><span>Teachers</span></a></li>";
  print "          <li><a href='/students'><span>Students</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='active'><a href='/admin'><span class=\"fa fa-plus\"> Add</span></a>";
  print "       <ul>";
  print "          <li><a href='/addSemester'><span>add semester</span></a></li>";
  print "          <li><a href='/addCourse'><span>add course</span></a></li>";
  print "          <li><a href='/addTeacher'><span>add teacher</span></a></li>";
  print "          <li><a href='/addStudent'><span>add student</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-magic\"> Active</span></a>";
  print "       <ul>";
  print "          <li><a href='/selectTerm'><span>semesters</span></a></li>";
  print "          <li><a href='/students'><span>all</span></a></li>";
  print "          <li><a href='/showActiveTas'><span>active</span></a></li>";
  print "          <li><a href='/showPreferences'><span>preferences</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-shopping-cart\"> Assignments</span></a>";
  print "       <ul>";
  print "          <li><a href='/assignments'><span>all terms</span></a></li>";
  print "          <li><a href='/showActiveAssignments?option=ALL'><span>active all</span></a></li>";
  print "          <li><a href='/showActiveAssignments?option=TA'><span>active TAs</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-pencil-square-o\"> Evaluations</span></a>";
  print "       <ul>";
  print "          <li><a href='/evaluations'><span>all</span></a></li>";
  print "          <li><a href='/showActiveEvaluations'><span>active</span></a></li>";
  print "          <li><a href='/addEvaluation'><span>evaluate</span></a></li>";
  print "          <li><a href='/showEvaluators'><span>evaluators</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print "    <li class='has-sub'><a href='#'><span class=\"fa fa-spinner\"> Misc</span></a>";
  print "       <ul>";
  print "          <li><a href='/email'><span>email</span></a></li>";
  print "          <li><a href='/showAccessLists'><span>access lists</span></a></li>";
  print "          <li><a href='/plots'><span>plots</span></a></li>";
  print "       </ul>";
  print "    </li>";
  print " </ul>";
  print " </div>";
  print "</div>"."\n";
}
?>
