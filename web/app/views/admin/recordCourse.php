<?php

include("app/views/admin/header.php");

include("app/models/Dbc.php");
include("app/models/Course.php");

// make sure this is the master
if (! (isMaster())) { 
  exitAccessError();
}

$db = Dbc::getReader();
$course = Course::fromNumber($db,$_POST['number']);
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

$db = Dbc::getReader();
if ($new)
  $course->addToDb($db);
else
  $course->updateDb($db);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
