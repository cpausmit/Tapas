<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

include("app/models/Course.php");

// Pick up the course number from the form
$number = $_POST['number'];

// See whether this is a known student
$course = Course::fromNumber($number);
$new = $course->isFresh();

print '<article class="page">'."\n";
if ($new)
  print '<h1>Add a New Course to the Database</h1>'."\n";
else
  print '<h1>Update Existing Course in the Database</h1>'."\n";
print ' '."\n";

// initialize the variables
$name = '';
$version = '';
if (! $new) {
  $name = $course->name;
  $version = $course->version;
}

print '<table>';
print '<form  action="/recordCourse" method="post">'."\n";
print '<tr><td>';
print ' <b>FIXED NUMBER</b> ';
print '</td><td>';
print '<select class="number" name="number">'."\n";
print '<option value="'.$number.'">'.$number.'</option>'."\n";
print '</select>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td>';
print '  Name:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="name" value="'.$name.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Version:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="version" value="'.$version.'"><br>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td></td><td>';

// make sure to specify the type of action
if ($new)
  print '<input type="submit" value="submit new course record" />'."\n";
else
  print '<input type="submit" value="submit updated course record" />'."\n";

print '</td></tr>';
print '</table>';
print '</form>'."\n";

print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
