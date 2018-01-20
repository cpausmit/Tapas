<?php

include("app/views/ta/header.php");

// make sure we are dealing with a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");

// connect to our database
$db = Dbc::getReader();

// find the active tables
$activeTables = new Tables(Dbc::getReader(),"ActiveTables");
$term = substr($activeTables->getUniqueMatchingName('Preferences'),-5,5);

print '<article class="page">'."\n";
print '<h1>TA Preference Removal</h1>';

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
$sql = "delete from Preferences where Term='$term' and Email = '$email'";

$rc = $db->exec($sql);
if (!$rc) {
  //$errNum = mysqli_errno($link);
  //$errMsg = mysqli_error($link);
  print " ERROR - could not remove preferences: ";
}
else {
  print '<p>Selected preferences have been removed.</p>';
}

print '</article>'."\n";

include("app/views/ta/footer.php");

?>
