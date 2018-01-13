<?php

// make sure we are dealing with a administrator
include("app/views/admin/header.php");
if (! (isMaster() || isAdmin())) { 
  exitAccessError();
}

include_once("app/models/Dbc.php");
include_once("app/models/Tables.php");
include_once("app/models/Student.php");
include_once("app/models/Ta.php");

// Make sure the email parameter is properly expanded into a valid email address
function findEmail($email)
{
  // Make sure that the email makes sense (add '@mit.edu' if not provided)
  $pos = strpos($email,'@');
  if ($pos === false)                 // careful triple '='
    $email = $email . '@mit.edu';

  return $email;
}

// Get all students from the database
function getStudentsFromDb($db)
{
  // read complete students table
  $students = "";
  $rows = $db->query("select * from Students order by lastName");
  foreach ($rows as $key => $row) {
    $student = Student::fromRow($row);
    $students[$student->email] = $student;
  }

  return $students;
}

// Get all TAs that are in the planning table
function getPlanningTable($db)
{
  // find the planning table
  $planningTables = new Tables($db,"PlanningTables");
  $taTable = $planningTables->getUniqueMatchingName('Tas');

  return $taTable;
}

// Get all TAs that are in the planning table
function getTasFromDb($db)
{
  // see whether this student is already a planned TA

  $taTable = getPlanningTable($db);

  print " Planning table: $taTable";

  // do the query
  $rows = $db->query("select Email, Fulltime, PartTime from $taTable order by Email");
  $tas = "";
  foreach ($rows as $key => $row) {
    $ta = Ta::fromRow($row);
    $tas[$ta->email] = $ta;
  }

  return $tas;
}

function printForm()
{
  print '<form  action="/planTas" method="post">'."\n";
  print '<tr>';
  print '<td align=center>';
  print '<input type="submit" value="submit" style="width:100%" />'."\n";
  print '</td><td>';
  print '  <input type="text" name="email" placeholder="jane_doe"><br>'."\n";
  print '</td>';
  print '<td align=center><select class="type" name="fullTime">'."\n";
  print '    <option value="1">1</option>'."\n";
  print '    <option value="0">0</option>';
  print '    </select>'."\n";
  print '</td>';
  print '<td align=center><select class="type" name="partTime">'."\n";
  print '    <option value="0">0</option>'."\n";
  print '    <option value="1">1</option>';
  print '    </select>'."\n";
  print '</td>';
  print '</tr>';
  print '</form>'."\n";
}

function removeFromDb($db,$taTable,$email)
{
  // remove an existing student from the database
  $sql = " delete from $taTable where email = '$email'";
  $db->Exec($sql);
}

// start the html page
print '<article class="page">'."\n";

// connect to our database
$db = Dbc::getReader();

// get a full list of students
$students = getStudentsFromDb($db);

// the planning TA table
$taTable = getPlanningTable($db);

// get list of TAs in our planning table
$tas = getTasFromDb($db);
$nTas = sizeof($tas);

// pick up email, effort and action from the form
if (array_key_exists('email',$_POST) &&
    array_key_exists('fullTime',$_POST) && array_key_exists('partTime',$_POST)  ) {

  $email = $_POST['email'];
  $fullTime =  $_POST['fullTime'];
  $partTime =  $_POST['partTime'];

  // Make sure that the email makes sense (add '@mit.edu' if not provided)
  $email = findEmail($email);

  if (! isset($students[$email])) {
    print "<h1>ERROR</h1>\n";
    print "This email is not in our database. \n";
    print "<h3><a href=\"/updateStudent?email=$email\">Please first add the student here.";
    print "</a></h3><br>\n";
  }
  else {
    if (isset($tas[$email])) {
      removeFromDb($db,$taTable,$email);
      print "<p>Removed TA from our list: $email</p>\n";
    }
    else {
      $ta = Ta::fresh();
      $ta->email = $email;
      $ta->fullTime = $fullTime;
      $ta->partTime = $partTime;
      $ta->addToDb($db,$taTable);
      print "<p>Added new TA to our list: $email</p>\n";
    }
    // update the TA list in memory
    $tas = getTasFromDb($db);
    $nTas = sizeof($tas);
  }
}

print "<article class=\"page\">\n";

print "<h1>TA List ($taTable)</h1>\n";
print "<hr>\n";

print "<table>\n";

// loop through all TAs
if ($nTas > 0 && $tas != "") {
  $first = true;
  foreach ($tas as $key => $ta) {
    if (isset($students[$ta->email])) {
      $student = $students[$ta->email];
      if ($first) {
        $first = false;
        printForm();
        $student->printTableHeader(true);
        print "<th>&nbsp; FullTime &nbsp;</th><th>&nbsp; PartTime &nbsp;</th></tr>";
      }

      $student->printTableRow(true);
      print "<td align=center>&nbsp;$ta->fullTime</td><td align=center>&nbsp;$ta->partTime</td></tr>";
    }
    else                                     // should never happen, but checking is better
      print "<br><b> ERROR -- student not found in database: $ta->email</b><br>";
  }
  print "</table>\n";
  print "<p> &nbsp;&nbsp;&nbsp;&nbsp; $nTas unique entries (in: $taTable).</p>";
  print "<hr>\n";
}

// footer
print "<hr>\n";
print '</article>'."\n";

include("app/views/admin/footer.php");

?>
