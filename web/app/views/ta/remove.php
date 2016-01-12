<?php

include("app/views/ta/header.php");

// make sure we are dealing with a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>TA Preference Removal</h1>';

// connect to our database
$link = getLink();

// find the active tables
$tableNames = findActiveTable($link,'Preferences');
$activeTable = $tableNames[0];

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
$query = "delete from $activeTable where Email = '" . $email . "'";
$statement = $link->prepare($query);
$rc = $statement->execute();
if (!$rc) {
  $errNum = mysqli_errno($link);
  $errMsg = mysqli_error($link);
  print " ERROR - could not remove preferences: ErrNo=" . $errNum . ": " . $errMsg . "\n";
}
else {
  print '<p>Selected preferences have been removed.</p>';
}

print '</article>'."\n";

include("app/views/ta/footer.php");

?>
