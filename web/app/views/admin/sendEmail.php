<?php

include("app/views/admin/header.php");
if (! isMaster()) { 
  exitAccessError();
}

// command line arguments
$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

if (isset($_GET['to']))
  $targetString = $_GET['to'];
if (isset($_POST['Subject']))
  $subject = $_POST['Subject'];
if (isset($_POST['Message']))
  $message = $_POST['Message'];

$cc       = "Cc: paus@mit.edu,nergis@mit.edu,cmodica@mit.edu";
$headers  = "From: Christoph Paus <paus@mit.edu>\nReply-To: <paus@mit.edu>\n$cc";

// retrieve emails of recipients
include("app/models/Dbc.php");
$db = Dbc::getReader();
if ($targetString == "TAs") {
  // find active Ta table
  $activeTables = new ActiveTables($db);
  $taTable = $activeTables->getUniqueMatchingName('Tas');
  // do the query
  $rows = $db->query("select Email from $taTable");
  $list = "";
  foreach ($rows as $key => $row) {
    if ($list == "")
      $list = "$row[0]";
    else
      $list = "$list,$row[0]";
  }
} 

print '<article class="page">'."\n";
print "<hr>\n";
print "<h1>Sending the Email</h1>\n";
print "<h2> Christoph ($email)</h2>\n";
print "<hr>\n";
print " TO:<br>      $list<br>\n";
print " SUBJECT:<br> $subject<br>\n";
print " MESSAGE:<br> $message<br>\n";
print " HEADERS:<br> $headers<br>\n";

// Send
print "<p>Mail was NOT sent. Feature disabled for now!</p>";

//if (mail($list,$subject,$message,$headers))
//  print "<p>Mail accepted for delivery (does not guarantee it reaches its destination).</p>";
//else
//  print "<p>Mail was NOT accepted for delivery.</p>";

print "<hr>\n";
print "</article>\n";

include("app/views/admin/footer.php");

?>
