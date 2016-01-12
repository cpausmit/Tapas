<?php

include("app/views/admin/header.php");

// make sure we have a registered TA
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

print '<article class="page">'."\n";
print '<h1>Add a TA Evaluation</h1>'."\n";
print ' '."\n";

$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);

$term = 'undefined';
$teacherEmail = 'undefined';
$taEmail = 'undefined';

print '<p>Make your selections.</p>'."\n";
print '<table>';
print '<form  action="/recordEvaluation" method="post">'."\n";
print '<tr><td>';
print '  Term (ex. S2015):&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="term"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Teacher email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="teacherEmail"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  TA email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="taEmail"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '</td><td>';
print '</table>';
// start the form
print '<p>';
print "Write your evaluation text below.<br>\n";
print '<textarea  style="font-family: arial, verdana, sans-serif; font-size: 20px; color: black; background-color: white" name="evaluation" rows=8 cols=80></textarea><br>';
print '<input type="checkbox" name="award" value="1"> Propose award?';
print '&nbsp; if yes, please add a short citation for the potential award below.<br>';
print '<textarea style="font-family: arial, verdana, sans-serif; font-size: 20px; color: black; background-color: white" name="citation" rows=1 cols=40></textarea><br>';
print '<input type="submit" value="submit evaluation" />'."\n";
print '</form>'."\n";

print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
