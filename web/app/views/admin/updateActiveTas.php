<?php

include("app/views/admin/header.php");

include("app/models/Dbc.php");
include("app/models/ActiveTables.php");
include("app/models/Student.php");
include("app/models/Ta.php");

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
if ($new) {
  print "<h1>ERROR</h1>\n";
  print "This email is not in our database. \n";
  print "Please first add the student <a href=\"/addStudent\">here</a><br>\n";
}
else {
 
  // Assume it is a new TA
  $new = true;
  
  // See whether this student is already an active TA
  $activeTables = new ActiveTables($db);
  $taTable = $activeTables->getUniqueMatchingName('Tas');
  // do the query
  $rows = $db->query("select Email, Fulltime, PartTime from $taTable order by Email");
  $i = 0;
  $tas = "";
  foreach ($rows as $key => $row) {
    $ta = Ta::fromRow($row);
    
    // checking whether proposed TA is in the list already
    if ($ta->email == $email)
      $new = false;
    
    $tas[$i] = $ta;
    $i = $i + 1;
  }
  $nTas = $i;
  
  print "<p>Active assignment table: $taTable with $nTas Tas.</p>";
  if ($new)
    print "<p>New TA will be added: $email</p>\n";
  else
    print "<p>Proposed TA is already in our list: $email</p>\n";

  $student->printSummary();
  print "<br>\n";
}

print '</article>'."\n";

include("app/views/admin/footer.php");

?>
