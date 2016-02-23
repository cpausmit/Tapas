<?php

include("app/views/admin/header.php");

include("app/models/Dbc.php");
include("app/models/Student.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

// Pick up the email from the form
$email = $_POST['email'];

// See whether this is a known student
$db = Dbc::getReader();
$student = Student::fromEmail($db,$email);
$new = $student->isFresh();

print '<article class="page">'."\n";
if ($new)
  print '<h1>Add a New Student to the Database</h1>'."\n";
else
  print '<h1>Update Existing Student in the Database</h1>'."\n";
print ' '."\n";

// initialize the variables
$firstName = '';
$lastName = '';
//$email = '';
$aaEmail = '';
$svEmail = '';
$year = '';
$division = '';
$research = '';
if (! $new) {
  $firstName = $student->firstName;
  $lastName = $student->lastName;
  //$email = $student->email;
  $aaEmail = $student->advisorEmail;
  $svEmail = $student->supervisorEmail;
  $year = $student->year;
  $division = $student->division;
  $research = $student->research;
}


print '<table>';
print '<form  action="/recordStudent" method="post">'."\n";
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
print '  Academic Advisor email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="aaEmail" value="'.$aaEmail.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Supervisor email:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="svEmail" value="'.$svEmail.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Year joined:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="year" value="'.$year.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Division:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="division" value="'.$division.'"><br>'."\n";
print '</td></tr>';
print '<tr><td>';
print '  Research:&nbsp;'."\n";
print '</td><td>';
print '  <input type="text" name="research" value="'.$research.'"><br>'."\n";
print '</td></tr>';
print '<tr><td> ------ </td><td> </td></tr>';
print '<tr><td></td><td>';

// make sure to specify the type of action
if ($new)
  print '<input type="submit" value="submit new student record" />'."\n";
else
  print '<input type="submit" value="submit updated student record" />'."\n";

print '</td></tr>';
print '</table>';
print '</form>'."\n";

print '</p>'."\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
