<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

// command line arguments
$email = $_GET['email'];

// load database and models
include("app/models/Dbc.php");
include("app/models/TeachingTask.php");
include("app/models/Student.php");

// create an instance
$db = Dbc::getReader();
$students = $db->query("select * from Students where Email = '$email'");
$student = '';
foreach ($students as $key => $row)
  $student = Student::fromRow($row);

// connect to our database
$link = getLink();
$tables = findTables($link,'Assignments');

print '<article class="page">'."\n";
print "<hr>\n";
//print "<h1>TA Summary Page</h1>\n";
$student->printSummary();
print "\n";
print "<hr>\n";

// loop through all assignment tables and find our candidate
print '<ul>';
foreach ($tables as $key => $table) {
  $query = "select * from " . $table . " where Person='" . $email . "'";
  $statement = $link->prepare($query);
  $statement->execute();
  $statement->bind_result($taskId,$person);
  while ($statement->fetch()) {
    print '<li>';
    $task = new TeachingTask($taskId);
    $task->printTaskWithLink();
  }
}
print '</ul>';

$link = getLink();
$tables = findTables($link,'Evaluations');
print '<ul>';
foreach ($tables as $key => $table) {
  $query = "select TeacherEmail, EvalText from " . $table . " where TaEmail='" . $email . "'";
  $statement = $link->prepare($query);
  $statement->execute();
  $statement->bind_result($teacherEmail,$evalText);
  while ($statement->fetch()) {
    print '<li>';
    print "<b>$table -- $teacherEmail</b>:<br> $evalText<br>";
  }
}
print '</ul>';

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
