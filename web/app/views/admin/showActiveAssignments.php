<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Assignment.php");
include_once("app/models/Student.php");
include_once("app/models/Teacher.php");
include_once("app/models/Tables.php");

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


// command line arguments
$option = "ALL";
if (isset($_GET['option']))
  $option = $_GET['option'];  // this is the input course number

// find the active tables and the last non-active table
$activeTables = new Tables("ActiveTables");
$assignmentsTable = $activeTables->getUniqueMatchingName('Assignments');
$term = substr($assignmentsTable,-5,5);
$assignments = Assignments::fromDb($term);
$students = Students::fromDb();
$Teachers = Teachers::fromDb();

print '<article class="page">'."\n";
print '<h1>Show Active Assignments</h1>';
print ' ';
print "Unassigned slots from $assignmentsTable</p>";
$assignments->show("Unassigned");
print "Active Assignments from $assignmentsTable</p>";

//$assignments->show($option);

print "<table>\n";
print "<tr><th>&nbsp; Term &nbsp;</th><th>&nbsp; Course &nbsp;</th><th> Type &nbsp;</th><th> Effort &nbsp;</th>";
print "<th> TA type &nbsp;</th><th> Person &nbsp;</th><th> Id &nbsp;</th><th> EvalO &nbsp;</th></tr>\n";
$iF = 0;
$iP = 0;
foreach ($assignments->list as $task => $assignment) {
  $myTask = new TeachingTask($task);
  $p = $assignment->person;
  $display = 0;

  if ($option == "ALL" && $p != "EMPTY" && $p != "EMPTY@mit.edu")
    $display = 1;
  else if ($option == "TA" &&
           $myTask->isTa() && $p != "" && $p != "EMPTY" && $p != "EMPTY@mit.edu")
    $display = 1;
  else if ($option == "Unassigned" &&
           ($p == "" || $p == "EMPTY" || $p == "EMPTY@mit.edu"))
    $display = 1;

  if ($display) {
    if ($myTask->isTa()) {
      $student = $students->list[$assignment->person];
      show_student($assignment,$myTask,$student);
      // Accounting
      if ($myTask->getEffort() == 'full')
        $iF = $iF + 1;
      if ($myTask->getEffort() == 'half')
        $iF = $iF + 0.5;
      if ($myTask->getEffort() == 'part')
        $iP = $iP + 1;
    }
    else {
      $teacher = $teachers->list[$assignment->person];
      show_teacher($assignment,$myTask,$student);
    }
  }
}
print "</table>";
print "<p> TA openings ($option): <b>$iF</b> (full time)  <b>$iP</b> (part time).</p><br> \n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
