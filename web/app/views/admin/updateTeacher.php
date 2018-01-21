<?php

include("app/views/admin/header.php");

include_once("app/models/Teacher.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

// Pick up the email from the form
$email = makeEmail($_POST['email']);

// See whether this is a known teacher
$teacher = Teacher::fromEmail($email);
$new = $teacher->isFresh();

print '<article class="page">'."\n";
if ($new)
  print '<h1>Add a New Teacher to the Database</h1>'."\n";
else
  print '<h1>Update Existing Teacher in the Database</h1>'."\n";
print ' '."\n";

// initialize the variables
$firstName = '';
$lastName = '';
// from interface // $email = '';
$position = '';
$status = '';

if (! $new) {
  $firstName = $teacher->firstName;
  $lastName = $teacher->lastName;
  //$email = $teacher->email;
  $position = $teacher->position;
  $status = $teacher->status;
}


print '<table>';
print '<form  action="/recordTeacher" method="post">'."\n";
print '<tr><td>';
print ' <b>FIXED EMAIL</b> ';
print '</td><td>';
print '<select class="email" name="email">'."\n";
print '<option value="'.$email.'">'.$email.'</option>'."\n";
print '</select>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td>';
print '  First Name:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="firstName" value="'.$firstName.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Last Name:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="lastName" value="'.$lastName.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Position:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="position" value="'.$position.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Status:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="status" value="'.$status.'"><br>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td></td><td>';

// make sure to specify the type of action
if ($new)
  print '<input type="submit" value="submit new teacher record" />'."\n";
else
  print '<input type="submit" value="submit updated teacher record" />'."\n";

print '</td></tr>';
print '</table>';
print '</form>'."\n";

print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
