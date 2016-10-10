<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Add/Update Course in the Database</h1>'."\n";
print ' '."\n";

$email = 'undefined';

print '<table>';
print '<form  action="/updateCourse" method="post">'."\n";
print '<tr><td>';
print '  Number:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="number"><br>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td></td><td>';
print '<input type="submit" value="find course record" />'."\n";
print '</td></tr>';
print '</table>';
print '</form>'."\n";

print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
