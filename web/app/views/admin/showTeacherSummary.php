<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

// command line arguments
$email = $_GET['email'];

// load database and models
include_once("app/models/Assignment.php");
include_once("app/models/Teacher.php");
include_once("app/models/TeachingTask.php");

// find the stuff we need from the database
$teachers = Teachers::fromDb();
$teacher = $teachers->list[$email];

print '<article class="page">'."\n";
print "<hr>\n";
$teacher->printSummary();
print "\n";
print "<hr>\n";

// loop through all assignment tables and find our candidate
$assignments = Assignments::fromDb('ALL');
print '<ul>';
foreach ($assignments->list as $key => $assignment) {
  if ($assignment->person == $email) {
    print '<li>';
    $assignment->show('simple');
  }
}
print '</ul>';

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
