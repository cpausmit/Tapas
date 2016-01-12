<?php

include("app/views/ta/header.php");

// make sure we have a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Selected TA Preferences</h1>';

// connect to our database
$link = getLink();

// find the active tables
$tableNames = findActiveTable($link,'Preferences');
$activeTable = $tableNames[0];
print "<p>Active table: $activeTable &nbsp;&nbsp;<br>\n";

// Now we know the table to use

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
$query = "select * from $activeTable where Email='$email'";
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
