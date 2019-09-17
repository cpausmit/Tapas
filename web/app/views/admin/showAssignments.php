<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");
include_once("app/models/Student.php");
include_once("app/models/Teacher.php");

function show_student($assignment,$t,$student)
{
  // print the full assignment
  print "<tr>";
  print "<td> "
    . $assignment->term . "&nbsp;</td><td>"
    . "<a href=\"/showTaskSummary?number=" . $t->getCourse(). "\">"
    . $t->getCourse()
    . "</a>"
    . "&nbsp;</td><td>"
    . $t->getType()    . "&nbsp;</td><td>"
    . $t->getEffort()  . "&nbsp;</td><td>"
    . $t->getTaType()  . "&nbsp;</td><td>";

  print "<a href=\"/showTaSummary?email=" . $assignment->person . "\">"
    . $assignment->person
    . "</a>"
    . "&nbsp;</td><td>"
    . $student->division . "&nbsp;&nbsp;</td><td>"
    . $t->generateId() . "&nbsp;</td>\n"
   . "<td>&nbsp; " . number_format($assignment->evalO,1,'.',',') . " &nbsp;</td>";
  print "</tr>\n";
}

function show_teacher($assignment,$t,$teacher)
{
  // print the full assignment
  print "<tr>";
  print "<td> "
    . $assignment->term . "&nbsp;</td><td>"
    . "<a href=\"/showTaskSummary?number=" . $t->getCourse(). "\">"
    . $t->getCourse()
    . "</a>"
    . "&nbsp;</td><td>"
    . $t->getType()    . "&nbsp;</td><td>"
    . $t->getEffort()  . "&nbsp;</td><td>"
    . $t->getTaType()  . "&nbsp;</td><td>";

  print "<a href=\"/showTeacherSummary?email=" . $assignment->person . "\">"
    . $assignment->person
    . "</a>"
    . "&nbsp;</td><td>"
    . $teacher->status . "&nbsp;&nbsp;</td><td>"
    . $t->generateId() . "&nbsp;</td>\n"
   . "<td>&nbsp; " . number_format($assignment->evalO,1,'.',',') . " &nbsp;</td>";
  print "</tr>\n";
}

// command line parameters
$term = $_GET['term'];
$assignments = Assignments::fromDb($term);
$students = Students::fromDb();
$teachers = Teachers::fromDb();

print '<article class="page">'."\n";
print "<h1>Show All Assignments - $term</h1>";
print ' ';
print ' ';

print "<table>\n";
print "<tr><th>&nbsp; Term &nbsp;</th><th>&nbsp; Course &nbsp;</th><th> Type &nbsp;</th><th> Effort &nbsp;</th>";
print "<th> TA type &nbsp;</th><th> Person &nbsp;</th><th> Division &nbsp;</th><th> Id &nbsp;</th><th> EvalO &nbsp;</th></tr>\n";

foreach ($assignments->list as $task => $assignment) {
  $myTask = new TeachingTask($task);

  if ($myTask->isTa()) {
    $student = $students->list[$assignment->person];
    show_student($assignment,$myTask,$student);
  }
  else {
      //$assignment->show();
    $teacher = $teachers->list[$assignment->person];
    show_teacher($assignment,$myTask,$teacher);
  }
}
print "</table>";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
