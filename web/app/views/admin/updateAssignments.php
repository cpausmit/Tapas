<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

$term = $_GET['term'];

print '<article class="page">'."\n";
print "<h1>Update TA Assignments - $term</h1>";
print ' ';

// connect to our database
$link = getLink();

// show last assignments
updateAssignment($link,'Assignments'.$term);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
