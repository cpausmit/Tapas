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
include_once("app/models/Teacher.php");
include_once("app/models/TeachingTask.php");

// create an instance
$db = Dbc::getReader();

// find the stuff we need from the database
$teachers = Teachers::fromDb($db);
$teacher = $teachers->list[$email];

// connect to our database
$tables = getTables($db,'Assignments_____');

print '<article class="page">'."\n";
print "<hr>\n";
$teacher->printSummary();
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
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
