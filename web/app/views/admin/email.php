<?php

include("app/views/admin/header.php");

if (! isMaster()) { 
  exitAccessError();
}

include_once("app/models/Assignment.php");
include_once("app/models/Ta.php");
include_once("app/models/Tables.php");

function findRecipientList($targetString,$email)
{
  $list = "";
  if ($targetString == "TAs") {
    // find planning Ta table
    $planningTables = new Tables("PlanningTables");
    $term = substr($planningTables->getUniqueMatchingName('Tas'),-5,5);
    $tas = Tas::fromDb($term);
    foreach ($tas->list as $key => $ta) {
      if ($list == "")
        $list = "$ta->email";
      else
        $list = "$list,$ta->email";
    }
  } 
  else if ($targetString == "Teachers") {
    // find evaluations teachers
    $activeTables = new Tables("ActiveTables");
    $term = substr($activeTables->getUniqueMatchingName('Evaluations'),-5,5);
    $assignments = Assignments::fromDb($term);
    foreach ($assignments->list as $key => $assignment) {
      if (strpos($assignment->task,'-Lec-') > 0) {     // find only lecturers
        if ($list == "")
          $list = "$assignment->person";
        else
          $list = "$list,$assignment->person";
      }
    }
  } 
  else if ($targetString == "Myself") {
    $list = "$email";
  }

  return $list;
}

function isMessageReady()
{
  return (isset($_POST['Action']) && isset($_POST['Recipients']) && isset($_POST['Subject']) && isset($_POST['Message']));
}

function printEmailForm($subject,$message)
{
  $style = 'style="font-family: arial; font-size: 20px; color: black;"';

  print '<p>';
  print "<form action=\"/email\" method=\"post\">\n";
  print '  <select class="Recipients" name="Recipients">'."\n";
  print '    <option value="">To: </option>'."\n";
  print "    <option value=\"Myself\"> Myself </option>";
  print "    <option value=\"TAs\"> TAs </option>";
  print "    <option value=\"Teachers\"> Teachers </option>";
  print '    </select>'."\n";
  print "Write your message.\n";
  if ($subject == "")
    print '<textarea placeholder="Subject: " '.$style.' name="Subject" rows=1 cols=40>'.
        $subject.'</textarea>';
  else
    print '<textarea '.$style.' name="Subject" rows=1 cols=40>'.$subject.'</textarea>';
  if ($message == "")
    print '<textarea placeholder="Message: "'.$style.' name="Message" rows=8 cols=80>'.
        $message.'</textarea>';
  else
    print '<textarea '.$style.' name="Message" rows=8 cols=80>'.$message.'</textarea>';
  print '<input type="submit" name="Action" value="show" />'."\n";
  print '<input type="submit" name="Action" value="send" />'."\n";
  print '</form>'."\n";
  print '</p>'."\n";
}

//==================================================================================================
// M A I N
//==================================================================================================

// setting the right defaults
$subject = '';
$message = '';

// command line arguments
$email = strtolower(strtolower($_SERVER['eppn']));
//$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

if (isset($_POST['Recipients']))
  $targetString = $_POST['Recipients'];
if (isset($_POST['Subject']))
  $subject = $_POST['Subject'];
if (isset($_POST['Message']))
  $message = $_POST['Message'];

//$headers  = "From: Christoph Paus <paus@mit.edu>\nReply-To: <paus@mit.edu>\n";

$cc       = "Cc: paus@mit.edu";
//$cc       = "Cc: paus@mit.edu,sahughes@mit.edu,cmodica@mit.edu";
$headers  = "From: Christoph Paus <paus@mit.edu>\nReply-To: <paus@mit.edu>\n".$cc."";

// are we sending the message already?
if (isMessageReady()) {
  $list = findRecipientList($targetString,$email);
  print "";
}

// start page
print '<article class="page">'."\n";
print "<hr>\n";

if (isMessageReady()) {
  foreach ($_POST as $key => $value) {
    echo ' - '.$key.' :: '.$value.'</br>';
  }

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

  if (strcmp($_POST['Action'],'send') == 0) {
    // Send
    //print " MESSAGE NOT SEND. DISABLED = ".$_POST['Action'];
    if (mail($list,$subject,$message,$headers))
      print "<p>Mail accepted for delivery (does not guarantee delivery though).</p>";
    else
      print "<p>Mail was NOT accepted for delivery.</p>";
  }
  else {
    print " MESSAGE NOT SEND. Action: ".$_POST['Action'];
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
