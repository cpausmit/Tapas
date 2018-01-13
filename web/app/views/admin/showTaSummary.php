<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

// command line arguments
$email = $_GET['email'];

// load database and models
include_once("app/models/Dbc.php");
include_once("app/models/TeachingTask.php");
include_once("app/models/Student.php");

$db = Dbc::getReader();

// create an instance
$students = Students::fromDb($db);
$student = $students->list[$email];

// connect to our database
$tables = getTables($db,'Assignments_____');

print '<article class="page">'."\n";
print "<hr>\n";
$student->printSummary();
print "\n";
print "<hr>\n";

// loop through all assignment tables and find our candidate
print '<ul>';
foreach ($tables as $key => $table) {
  $sql = "select * from " . $table . " where Person='" . $email . "'";
  $rows = $db->query($sql);
  foreach ($rows as $key => $row) {
    $taskId = $row[0];
    $person = $row[1];
    print '<li>';
    $task = new TeachingTask($taskId);
    $task->printTaskWithLink();
  }
}
print '</ul>';

$tables = getTables($db,'Evaluations_____');
print '<ul>';
foreach ($tables as $key => $table) {
  $sql = "select TeacherEmail, EvalText from " . $table . " where TaEmail='" . $email . "'";
  $rows = $db->query($sql);
  foreach ($rows as $key => $row) {
    $teacherEmail = $row[0];
    $evalText = $row[1];
    print '<li>';
    print "<b>$table -- $teacherEmail</b>:<br> $evalText<br>";
  }
}
print '</ul>';

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
