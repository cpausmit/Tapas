<?php

include("app/views/admin/header.php");

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

// make sure we have an admin
if (! (isAdmin() || isMaster())) {
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Course.php");

$courses = Courses::fromDb(Dbc::getReader());


print '<article class="page">';
print '<hr>';
print '<h1>Course Listing</h1>';

print '<p>';
print '<table>';

foreach ($courses->list as $number => $course) {
  print '<tr><td><a href="showTaskSummary?number=' . $course->number . '">' .$course->number .
    '</a></td><td>&nbsp;&nbsp;'
    . $course->name . "</td></tr>";
}
print '</table>';
print '</p>';

print '<hr>';
print '</article>';

include("app/views/admin/footer.php");

?>
