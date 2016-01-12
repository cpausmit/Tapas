<?php

include("app/views/admin/header.php");

// make sure we are dealing with a registered TA
if (!isMaster()) { 
  exitAccessError();
}

$link = getLink();
$levels = readAdminTable();
$tas = readFullTaTable($link);
$teachers = readTeacherTable($link);

print '<article class="page">'."\n";

// Admins
print "<h1>Admins</h1><p>";
foreach ($levels as $admin => $level) {
  print "&nbsp;&nbsp; $level -- $admin<br>";
}
print "</p>";

// TA table
print "<h1>TAs</h1><p>";
foreach ($tas as $key => $ta) {
  print "&nbsp;&nbsp; $ta<br>";
}
print "</p>";

// Teacher table
print "<h1>Teachers</h1><p>";
foreach ($teachers as $key => $teacher) {
  print "&nbsp;&nbsp; $teacher<br>";
}
print "</p>";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
