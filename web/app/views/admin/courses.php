<?php

include("app/views/admin/header.php");

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

// make sure we have an admin
if (! (isAdmin() || isMaster())) {
  exitAccessError();
}

print '<article class="page">';
print '<hr>';
print '<h1>Course Listing</h1>';

$link = getLink();

$query = 'select * from Courses';
$statement = $link->prepare($query);
$statement->execute();
$statement->bind_result($number,$name,$version);
print '<p>';
print '<table>';
while ($statement->fetch()) {
  print '<tr><td><a href="showTaskSummary?number=' . $number . '">' .$number .
    '</a></td><td>&nbsp;&nbsp;'
    . $name . "</td></tr>";
}
print '</table>';
print '</p>';

print '<hr>';
print '</article>';


include("app/views/admin/footer.php");

?>
