<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Copy last years TA Assignments</h1>';
print ' ';

// connect to our database
$link = getLink();

// find active tables
$active = findActiveTable($link,'Assignments');

if (sizeof($active) == 1) {
  // find last non-active table (one year ago, not one semester)
  $pattern = substr($active[0],0,-4);
  $last = findLastTable($link,$pattern,$active[0]);

  // show last assignments
  copyAssignment($link,$last,$active[0]);
}
else {
  if (sizeof($active) < 1) {
    print ' ERROR - no active Assignment table. EXIT';
  }
  else {
    print ' ERROR - more than one active Assignment table. EXIT';
  }
}

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
