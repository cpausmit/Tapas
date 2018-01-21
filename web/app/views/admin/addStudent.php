<?php

include("app/views/admin/header.php");

// make sure we have an admin
if (! (isAdmin() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Student.php");

function printEmptyForm()
{
  print '<h1>Add/Update Student in the Database</h1>'."\n";
  print ' '."\n";
  print '<table>';
  print '<form  action="/addStudent" method="post">'."\n";
  print '<tr><td>';
  print '  Email:&nbsp;'."\n";
  print '</td><td>';
  print '  <input type="text" name="email"><br>'."\n";
  print '</td></tr>';
  print '<tr><td> ------ </td><td> </td></tr>';
  print '<tr><td></td><td>';
  print '<input type="submit" value="find student record" />'."\n";
  print '</td></tr>';
  print '</table>';
  print '</form>'."\n";
}

// Pick up the email from the form
$email = '';
if (array_key_exists('email',$_REQUEST))
  $email = makeEmail($_REQUEST['email']);

$students = Students::fromDb();

// start the page
print '<article class="page">'."\n";

if ($email == '') {
  printEmptyForm();
}
else if (array_key_exists('firstName',$_REQUEST)) {
  // Set all relevant variables
  $student = Student::fresh();
  $student->firstName = $_POST['firstName'];
  $student->lastName = $_POST['lastName'];
  $student->email = $_POST['email'];
  $student->advisorEmail = $_POST['advisorEmail'];
  $student->supervisorEmail = $_POST['supervisorEmail'];
  $student->year = $_POST['year'];
  $student->division = $_POST['division'];
  $student->research = $_POST['research'];

  $test = Student::fromEmail($student->email);
  if ($test->isFresh()) {
    $student->addToDb();
    print ' New student added to database: <br>'."\n";
  }
  else {
    $student->updateDb();
    print ' Updated existing student in the database: <br>'."\n";
  }
  $student->printSummary();
  printEmptyForm();
}
else {
  // See whether this is a known student
  $student = Student::fromEmail($email);
  if ($student->isFresh())
    print '<h1>Add a New Student to the Database</h1>'."\n";
  else
    print '<h1>Update Existing Student in the Database</h1>'."\n";
  print ' '."\n";
  $student->printStudentForm('/addStudent');
}

// For reference all available students
print "<hr>\n";
print "<h1>Graduate Student TA Listing</h1>\n";
print "<hr>\n";
print "<table>\n";

// loop
$first = true;
foreach ($students->list as $key => $student) {
  $student->printTableRow($first);
  $first = false;
}

// footer
print "</table>\n";
print "<hr>\n";

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
