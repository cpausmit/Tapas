<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Add Active TA to the Database</h1>'."\n";
print ' '."\n";

$email = 'undefined';

print '<table>';
print '<form  action="/updateActiveTas" method="post">'."\n";
print '<tr><td>';
print '  Email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="email"><br>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td></td><td>';
print '<input type="submit" value="add active TA" />'."\n";
print '</td></tr>';
print '</table>';
print '</form>'."\n";

print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
