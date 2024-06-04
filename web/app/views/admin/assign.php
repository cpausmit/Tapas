<?php

include("app/views/admin/header.php");

// make sure we have a registered TA
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");
include_once("app/models/Course.php");
include_once("app/models/Student.php");
include_once("app/models/Ta.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

function adjustAssignments($term)
{
  // read assignments from database, make required adjustments in DB and read the newest version back
  $assignments = Assignments::fromDb($term);
  if (!isset($_POST['ta']) || !isset($_POST['assignment'])) {
    return $assignments;
  }

  // An assignment action is requested
  if (isset($assignments->list[$_POST['assignment']])) {
    $clear  = 0;
    if (strcmp($assignments->list[$_POST['assignment']]->person,$_POST['ta']) == 0)
      $clear = 1;
    
    // clear this TA from all assignments
    foreach ($assignments->list as $key => $assignment) {
      if (strcmp($assignment->person,$_POST['ta']) == 0) {
        print "&nbsp;&nbsp; Cleared " . $_POST['ta'] . " from " . $assignment->task . "<br>\n";
        $assignment = new Assignment();
        $assignment->term = $term;
        $assignment->task = $key;
        $assignment->person = 'EMPTY';
        $assignment->evalO = -1.0;
        $assignment->updateDb();
      }
    }
    if ($clear == 0) {
      print "&nbsp;&nbsp; Assign " . $_POST['ta'] . " to " . $_POST['assignment'] . "<br>\n";
      // update the assignment
      $assignment = new Assignment();
      $assignment->term = $term;
      $assignment->task = $_POST['assignment'];
      $assignment->person = $_POST['ta'];
      $assignment->evalO = -1.0;
      $assignment->updateDb();
    }
  }
  return Assignments::fromDb($term);
}

function generateOptions($assignments,$courses)
{
  // generate the options for all teaching assignment
  $options = array();
  foreach ($assignments->list as $key => $assignment) {
    $myTask = new TeachingTask($assignment->task);
    if ($myTask->isTa() && $myTask->getEffort() != 'part') {
      $number = $myTask->getCourse();
      $course = $courses->list[$number];
      $option = $myTask->getTaTask() . ': ' . $course->name;
      $find = array_search($option,$options);
      //if (! $find)
      //  $options[$assignment->task] = $option;
      if (!strpos($assignment->person,'@')) {
        $options[$assignment->task] = $option;
      }
    }
  }
  return $options;
}

function printAssignField($name,$assignedTask,$options)
{
  print '<form class="assign" action="/assign" method="post">'."\n";
  print '<input type="hidden" name="ta" value="'.$name.'">';
  print '<select class="assign" name="assignment">'."\n";
  print '  <option value="'.$assignedTask.'">'.$assignedTask.'</option>'."\n";
  foreach ($options as $key => $option) {
    print "<option value=\"" . $key . "\"> $key -- $option </option>";
  }
  print "</td><td>";
  if (strcmp($assignedTask,'EMPTY') == 0)
    print '<input class="home" type="submit" value="assign" />'."\n";
  else
    print '<input class="home" type="submit" value="clear" />'."\n";
  print '</form>'."\n";
  return;
}

// get the list of courses
$courses = Courses::fromDb();

// get all TAs and the possible full time assignments
$planningTables = new Tables('PlanningTables');
$term = substr($planningTables->getUniqueMatchingName('Assignments'),-5,5);
$students = Students::fromDb();
$tas = Tas::fromDb($term);
$assignments = adjustAssignments($term);

// start the page
print '<article class="page">'."\n";
print "<h1>Assign TAs for $term</h1>\n";
print ' '."\n";

// select all possible options for the full time TAs
$options = generateOptions($assignments,$courses);

// start the table
print '<p>'."\n";
print '<table class="assign" cellborder="1">'."\n";
$student = Student::fresh();
$student->printTableHeader(true);
print "<th>&nbsp; FullTime &nbsp;</th><th>&nbsp; PartTime &nbsp;</th><th>&nbsp; Assign &nbsp;</th></tr>";

// loop through all TAs
$fT = 0.;
$pT = 0.;
foreach ($tas->list as $key => $ta) {
  if (isset($students->list[$ta->email])) {
    $student = $students->list[$ta->email];
    $student->printTableRow(true);
    print "<td align=center>&nbsp;$ta->fullTime</td><td align=center>&nbsp;$ta->partTime</td><td>";

    $assignedTask = 'EMPTY';
    foreach ($assignments->list as $key => $assignment) {
      if (strcmp($assignment->person,$student->email) == 0) {
        $assignedTask = $assignment->task;
      }
    }
    printAssignField($student->email,$assignedTask,$options);
    print "</td></tr>";
    $fT += $ta->fullTime;
    $pT += $ta->partTime;
  }
  else                                     // should never happen, but checking is better
    print "<br><b> ERROR -- student not found in database: $ta->email</b><br>";
}

$nTas = sizeof($tas->list);
print "</table>\n";
print "<p> &nbsp;&nbsp;&nbsp;&nbsp; TERM: $term - $nTas unique entries ($fT full, $pT part).</p>";

// complete the form
print '</p>'."\n";
print ' '."\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
