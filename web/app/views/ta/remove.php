<?php

include("app/views/ta/header.php");

// make sure we are dealing with a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Preference.php");
include_once("app/models/Utils.php");

// find the active tables
$activeTables = new Tables("ActiveTables");
$term = substr($activeTables->getUniqueMatchingName('Preferences'),-5,5);

print '<article class="page">'."\n";
print '<h1>TA Preference Removal</h1>';

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

$preference = new Preference();
$preference->term = $term;
$preference->email = $email;

$preference->deleteFromDb();

print ' REMOVED preferences..<br>';

print "</article>\n";

include("app/views/ta/footer.php");

?>
