<?php

include("app/views/admin/header.php");
if (! isMaster()) { 
  exitAccessError();
}

// command line arguments
$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

print '<article class="page">'."\n";
print "<hr>\n";
print "<h1>Composing an Email</h1>\n<h2> From $email</h2>\n";
print "<hr>\n";

// loop through all assignment tables and find our candidate

// Send
$subject = 'EMPTY SUBJECT';
$message = "\n\nCheers, Christoph";
$headers = "From: Christoph Paus <paus@mit.edu>\nReply-To: <paus@mit.edu>";
print " THE MESSAGE: $message<br>\nTO: $email<br>";

// start the form
print '<p>';
print "<form action=\"/sendEmail\" method=\"post\">\n";
print '  <select class="Recipients" name="Recipients">'."\n";
print '  <option value="">Select recipients</option>'."\n";
print "  <option value=\"Myself\"> Myself </option>";
print "  <option value=\"TAs\"> TAs </option>";
print "  <option value=\"Teachers\"> Teachers </option>";
print '  </select>'."\n";
print "Write a message to the active TAs below.\n";
print '<textarea style="font-family: arial; font-size: 20px; color: black;" name="Subject" rows=1 cols=40>'.$subject.'</textarea>';
print '<textarea style="font-family: arial; font-size: 20px; color: black;" name="Message" rows=8 cols=80>'.$message.'</textarea>';
print '<input type="submit" value="send email" />'."\n";
print '</form>'."\n";
print '</p>'."\n";

print '</ul>';
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
