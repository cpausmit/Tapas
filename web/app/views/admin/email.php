<?php

include("app/views/admin/header.php");
include("app/models/Dbc.php");
if (! isMaster()) { 
  exitAccessError();
}

function findRecipientList($db,$targetString,$email)
{
  $list = "";
  if ($targetString == "TAs") {
    // find active Ta table
    $activeTables = new ActiveTables($db);
    $taTable = $activeTables->getUniqueMatchingName('Tas');
    // do the query
    $rows = $db->query("select Email from $taTable");
    foreach ($rows as $key => $row) {
      if ($list == "")
        $list = "$row[0]";
      else
        $list = "$list,$row[0]";
    }
  } 
  else if ($targetString == "Teachers") {
    $link = getLink();
    // do the query
    $rows = readTeacherTable($link);
    foreach ($rows as $key => $row) {
      if ($list == "")
        $list = "$row";
      else
        $list = "$list,$row";
    }
  } 
  else if ($targetString == "Myself") {
    $list = "$email";
  }

  return $list;
}

function isMessageReady()
{
  return (isset($_POST['Recipients']) &&
          isset($_POST['Subject']) && isset($_POST['Message']));
}

function printEmailForm($subject,$message)
{
  $style = 'style="font-family: arial; font-size: 20px; color: black;"';

  print '<p>';
  print "<form action=\"/email\" method=\"post\">\n";
  print '  <select class="Recipients" name="Recipients">'."\n";
  print '  <option value="">To: </option>'."\n";
  print "  <option value=\"Myself\"> Myself </option>";
  print "  <option value=\"TAs\"> TAs </option>";
  print "  <option value=\"Teachers\"> Teachers </option>";
  print '  </select>'."\n";
  print "Write your message.\n";
  if ($subject == "")
    print '<textarea placeholder="Subject: " '.$style.' name="Subject" rows=1 cols=40>'.$subject.'</textarea>';
  else
    print '<textarea '.$style.' name="Subject" rows=1 cols=40>'.$subject.'</textarea>';
  if ($message == "")
    print '<textarea placeholder="Message: "'.$style.' name="Message" rows=8 cols=80>'.$message.'</textarea>';
  else
    print '<textarea '.$style.' name="Message" rows=8 cols=80>'.$message.'</textarea>';
  print '<input type="submit" value="send email" />'."\n";
  print '</form>'."\n";
  print '</p>'."\n";
}

//==================================================================================================
// M A I N
//==================================================================================================

// connect to our database
$db = Dbc::getReader();

// setting the right defaults
$subject = '';
$message = '';

// command line arguments
$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

if (isset($_POST['Recipients']))
  $targetString = $_POST['Recipients'];
if (isset($_POST['Subject']))
  $subject = $_POST['Subject'];
if (isset($_POST['Message']))
  $message = $_POST['Message'];

$headers  = "From: Christoph Paus <paus@mit.edu>\nReply-To: <paus@mit.edu>\n";
//$cc       = "Cc: paus@mit.edu,sahughes@mit.edu,cmodica@mit.edu";
//$headers  = "From: Christoph Paus <paus@mit.edu>\nReply-To: <paus@mit.edu>\n$cc";

// are we sending the message already?
if (isMessageReady()) {
  $list = findRecipientList($db,$targetString,$email);
}

// start page
print '<article class="page">'."\n";
print "<hr>\n";

if (isMessageReady()) {
  $Uheaders = htmlentities($headers);
  // show what we are going to do
  print "<hr>\n";
  print "<h1>Sending this Email</h1>\n";
  print "<h2> From: Christoph ($email)</h2>\n";
  print "<hr>\n";
  print " IdString:    $targetString<br><br>\n";
  print " To:          $list<br>\n";
  print " Subject:     $subject<br>\n";
  print " Message:<br> $message<br>\n";
  print " Headers:<br> $Uheaders<br>\n";
  // Send
  print '<br><b>==== RESULT ====</b><br>'."\n";
  if (False) { 
    print "<p>Mail was NOT sent. Feature disabled for now!</p>";
  }
  else {
   if (mail($list,$subject,$message,$headers))
     print "<p>Mail accepted for delivery (does not guarantee delivery though).</p>";
   else
     print "<p>Mail was NOT accepted for delivery.</p>";
  }
}
print "<hr>\n";
print "<h1>Compose an Email</h1>\n";
print "<h2> From: $email</h2>\n";
print "<hr>\n";
printEmailForm($subject,$message);

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
