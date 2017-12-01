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
include("app/models/Teacher.php");

// create an instance
$db = Dbc::getReader();
$teachers = $db->query("select * from Teachers where Email = '$email'");
$teacher = '';
foreach ($teachers as $key => $row)
  $teacher = Teacher::fromRow($row);

// connect to our database
$link = getLink();
$tables = findTables($link,'Assignments');

print '<article class="page">'."\n";
print "<hr>\n";
$teacher->printSummary();
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
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
