<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Add a Students to the Database</h1>'."\n";
print ' '."\n";

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

$firstName = 'undefined';
$lastName = 'undefined';
$email = 'undefined';
$aaEmail = 'undefined';
$svEmail = 'undefined';
$division = 'undefined';
$research = 'undefined';

print '<table>';
print '<form  action="/recordStudent" method="post">'."\n";
print '<tr><td>';
print '  First Name:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="firstName"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Last Name:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="lastName"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="email"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Academic Advisor email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="aaEmail"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Supervisor email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="svEmail"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Division:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="division"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Research:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="research"><br>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td></td><td>';
print '<input type="submit" value="submit new student record" />'."\n";
print '</td></tr>';
print '</table>';
print '</form>'."\n";

print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
