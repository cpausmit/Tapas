<?php

include("app/views/ta/header.php");

// make sure we have a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Preference.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");

// get the db link
$email = strtolower(strtolower($_SERVER['eppn']));
//$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

// get all TAs and the possible full time assignments
$planningTables = new Tables('PlanningTables');
$term = substr($planningTables->getUniqueMatchingName('Preferences'),-5,5);
$preferences = Preferences::fromDb($term);

$empty = true;
if (isset($preferences->list[$email])) {
  $preference = $preferences->list[$email];
  $empty = false;
}

print '<article class="page">'."\n";
print '<h1>Selected TA Preferences</h1>';
print "<p>Planning term: $term &nbsp;&nbsp;<br>\n";
print 'Your registered TA preferences are:';
print ' ';

if ($empty) {
  print 'EMPTY.</p>';
}
else{
  print '</p><p>';

  $task1 = new TeachingTask($preference->pref1);
  print '&nbsp;&nbsp; 1: ';
  $task1->printTaTask();// print '<br>';

  $task2 = new TeachingTask($preference->pref2);
  print '&nbsp;&nbsp; 2: ';
  $task2->printTaTask();// print '<br>';

  $task3 = new TeachingTask($preference->pref3);
  print '&nbsp;&nbsp; 3: ';
  $task3->printTaTask();// print '<br>';

  print '</p> ';
  print "<p>&nbsp;&nbsp;  Comment: $preference->comment</p>";
}

print '</article>'."\n";
include("app/views/ta/footer.php");

?>
