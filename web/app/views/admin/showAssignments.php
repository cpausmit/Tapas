<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");

// command line parameters
$term = $_GET['term'];
$assignments = Assignments::fromDb($term);

print '<article class="page">'."\n";
print "<h1>Show TA Assignment - $term</h1>";
print ' ';
print ' ';
$assignments->show('ALL');

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
