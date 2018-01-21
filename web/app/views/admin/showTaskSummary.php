<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");
include_once("app/models/Course.php");
include_once("app/models/Student.php");
include_once("app/models/TeachingTask.php");

function getStudentName($students,$email)
{
  // Find the name for the person matching the email address

  $name = "";
  if (! isset($students->list[$email])) {
    if ($email == '')
      $name = 'NOT ASSIGNED';
    else
      print " person not found: $email<br>\n";
  }
  else {
    $student = $students->list[$email];
    $name = $student->lastName.", ".$student->firstName;
  }
  
  return $name;
}

function getCourseName($courses,$number)
{
  // Find the name for the course matching the course number

  $name = "";
  if (! isset($courses->list[$number])) {
    if ($number == '')
      $name = 'NOT ASSIGNED';
    else
      print " course number not found: $number<br>\n";
  }
  else
    $name = $courses->list[$number]->name;
  
  return $name;
}

// command line arguments
$number = $_GET['number'];  // this is the input course number

$students = Students::fromDb();
$courses = Courses::fromDb();
$courseName = getCourseName($courses,$number);

$findme = "-" . $number . "-Ta";

print '<article class="page">'."\n";
print "<hr>\n";
print "<h1>Course Task Summary Page</h1>\n<h2>$number $courseName</h2>";
print "<hr>\n";

print '<table>';

$assignments = Assignments::fromDb('ALL');
foreach ($assignments->list as $key => $assignment) {
  if (strpos($assignment->task,$findme) > 0) {
    $name = getStudentName($students,$assignment->person);
    // print result
    print '<tr><td>' . $name . '&nbsp;</td><td>&nbsp;'
      . '<a href="/showTaSummary?email=' . $assignment->person . '">'
      . $assignment->person . '</a>&nbsp;</td><td>';
    $myTask = new TeachingTask($assignment->task);
    $myTask->printTask();
    print "</td></tr>\n";
  }
}
print '</table>';

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
