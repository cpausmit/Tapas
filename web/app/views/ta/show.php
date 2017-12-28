<?php

include("app/views/ta/header.php");

// make sure we have a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include("app/models/Dbc.php");
include("app/models/Tables.php");
include("app/models/TeachingTask.php");

// get the db link
$db = Dbc::getReader();
$link = getLink();
$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

// get all TAs and the possible full time assignments
$planningTables = new Tables($db,'PlanningTables');
$preferencesTable = $planningTables->getUniqueMatchingName('Preferences');

print '<article class="page">'."\n";
print '<h1>Selected TA Preferences</h1>';
print "<p>Planning table: $preferencesTable &nbsp;&nbsp;<br>\n";

// Now we know the table to use
$query = "select * from $preferencesTable where Email='$email'";
$statement = $link->prepare($query);
$rc = $statement->execute();
if (!$rc) {
  $errNum = mysqli_errno($link);
  $errMsg = mysqli_error($link);
  print " ERROR - could not register selection: ErrNo=" . $errNum . ": " . $errMsg . "\n";
  exit();
}
$statement->bind_result($Email,$pref1,$pref2,$pref3);
$empty = true;

print 'Your registered TA preferences are:';
print ' ';

while ($statement->fetch()) {
  $empty = false;
  print '</p><p>';

  $task1 = new TeachingTask($pref1);
  print '&nbsp;&nbsp; 1: ';
  $task1->printTaTask();// print '<br>';

  $task2 = new TeachingTask($pref2);
  print '&nbsp;&nbsp; 2: ';
  $task2->printTaTask();// print '<br>';

  $task3 = new TeachingTask($pref3);
  print '&nbsp;&nbsp; 3: ';
  $task3->printTaTask();// print '<br>';

  print '</p> ';
}
if ($empty) {
  print 'EMPTY.</p>';
}

print '</article>'."\n";
include("app/views/ta/footer.php");

?>
