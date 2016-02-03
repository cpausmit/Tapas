<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

// command line parameters
$term = $_GET['term'];

print '<article class="page">'."\n";
print "<h1>Show TA Assignment - $term</h1>";
print ' ';

// connect to our database
$link = getLink();

// show assignments
showAssignment($link,'Assignments'.$term);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
