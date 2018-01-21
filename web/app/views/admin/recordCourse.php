<?php

include("app/views/admin/header.php");

include("app/models/Dbc.php");
include("app/models/Course.php");

// make sure this is the master
if (! (isMaster())) { 
  exitAccessError();
}

$course = Course::fromNumber($_POST['number']);
$new = $course->isFresh();

print '<article class="page">'."\n";
if ($new)
  print "<h1>New Course</h1>\n";
else
  print "<h1>Update Existing Course</h1>\n";

// Set all relevant variables
$course = Course::fresh();
$course->number = $_POST['number'];
$course->name = $_POST['name'];
$course->version = intval($_POST['version']);

if ($new)
  $course->addToDb();
else
  $course->updateDb();

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
