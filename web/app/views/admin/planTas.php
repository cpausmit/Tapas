<?php

// make sure we are dealing with a administrator
include("app/views/admin/header.php");
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Utils.php");
include_once("app/models/Dbc.php");
include_once("app/models/Tables.php");
include_once("app/models/Student.php");
include_once("app/models/Ta.php");

// Get all TAs that are in the planning table
function getPlanningTable()
{
  // find the planning table
  $planningTables = new Tables("PlanningTables");
  $taTable = $planningTables->getUniqueMatchingName('Tas');

  return $taTable;
}

function printForm()
{
  print '<form  action="/planTas" method="post">'."\n";
  print '<tr>';
  print '<td align=center>';
  print '<input type="submit" value="submit" style="width:100%" />'."\n";
  print '</td><td>';
  print '  <input type="text" name="email" placeholder="<email>"><br>'."\n";
  print '</td>';
  print '<td align=center>&nbsp; Full time: <select class="type" name="fullTime">'."\n";
  print '    <option value="1">1</option>'."\n";
  print '    <option value="0">0</option>';
  print '    </select>'."\n";
  print '</td>';
  print '<td align=center>&nbsp; Part time: <select class="type" name="partTime">'."\n";
  print '    <option value="0">0</option>'."\n";
  print '    <option value="1">1</option>';
  print '    </select>'."\n";
  print '</td>';
  print '</tr>';
  print '</form>'."\n";
}

// start the html page
print '<article class="page">'."\n";

// get input from the database
$students = Students::fromDb();
$taTable = getPlanningTable();
$term = substr($taTable,-5,5);
$tas = Tas::fromDb($term);

// pick up email, effort and action from the form
if (array_key_exists('email',$_POST) &&
    array_key_exists('fullTime',$_POST) &&
    array_key_exists('partTime',$_POST)    ) {

  $email = $_POST['email'];
  $fullTime =  $_POST['fullTime'];
  $partTime =  $_POST['partTime'];
  
  // Make sure that the email makes sense (add '@mit.edu' if not provided)
  $email = makeEmail($email);

  if (! isset($students->list[$email])) {
    print "<h1>ERROR</h1>\n";
    print "This email is not in our database. \n";
    print "<h3><a href=\"/addStudent?email=$email\">Please first add the student here.";
    print "</a></h3><br>\n";
  }
  else {
    if (isset($tas->list[$email])) {
      $ta = $tas->list[$email];
      $ta->removeFromDb();
      print "<p>Removed TA from our list (Tas, $term): $email</p>\n";
    }
    else {
      $ta = Ta::fresh();
      $ta->term = $term;
      $ta->email = $email;
      $ta->fullTime = $fullTime;
      $ta->partTime = $partTime;
      $ta->addToDb();
      print "<p>Added new TA to our list (Tas, $term): $email</p>\n";
    }
    // update the TA list in memory
    $tas = Tas::fromDb($term);
  }
}

print "<article class=\"page\">\n";

print "<h1>TA List (term: $term)</h1>\n";
print "<hr>\n";
print "<table>\n";

printForm();
$student = Student::fresh();
$student->printTableHeader(true);
print "<th>&nbsp; FullTime &nbsp;</th><th>&nbsp; PartTime &nbsp;</th></tr>";

// loop through all TAs

$fT = 0.;
$pT = 0.;
foreach ($tas->list as $key => $ta) {
  if (isset($students->list[$ta->email])) {
    $student = $students->list[$ta->email];
    $student->printTableRow(true);
    print "<td align=center>&nbsp;$ta->fullTime</td><td align=center>&nbsp;$ta->partTime</td></tr>";
    $fT += $ta->fullTime;
    $pT += $ta->partTime;
  }
  else                                     // should never happen, but checking is better
    print "<br><b> ERROR -- student not found in database: $ta->email</b><br>";
}

$nTas = sizeof($tas->list);
print "</table>\n";
print "<p> &nbsp;&nbsp;&nbsp;&nbsp; TERM: $term - $nTas unique entries ($fT full, $pT part).</p>";


// footer
print "<hr>\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
