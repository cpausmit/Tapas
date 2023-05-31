<?php

include("app/views/ta/header.php");

// make sure we are dealing with a registered TA
if (! (isTa() || isMaster())) { 
  exitAccessError();
}

include_once("app/models/Course.php");
include_once("app/models/Preference.php");
include_once("app/models/Tables.php");
include_once("app/models/TeachingTask.php");
include_once("app/models/Utils.php");

function parametersSet() {
  if (!isset($_POST['pref1']) || !isset($_POST['pref1']) || !isset($_POST['pref1'])) {
    return false;
  }
  return true;
}

function testSelection($p1,$p2,$p3) {
  $valid = false;
  // test validity of selection
  if ($p1 != "" && $p2 != "" && $p3 != "" &&     // part 1: make sure preferences are all set
      $p1 != $p2 && $p2 != $p3 && $p1 != $p3) {  // part 2: they cannot be duplicated
    $valid = true;
  } 
  return($valid);
}

// database pointer
$courses = Courses::fromDb();

// get all TAs and the possible full time assignments
$planningTables = new Tables('PlanningTables');
$term = substr($planningTables->getUniqueMatchingName('Preferences'),-5,5);
$preferences = Preferences::fromDb($term);
$email = strtolower(strtolower($_SERVER['eppn']));
//$email = strtolower($_SERVER['SSL_CLIENT_S_DN_Email']);
$comment = "";

print '<article class="page">'."\n";
print '<h1>Selected TA Preferences</h1>';

if (!parametersSet()) {
  print " Parameters not set.<br>";
  print '</article>'."\n";
  exit();
}
print '<p>Your selected TA preferences are:</p>';
print ' ';

print '<p>';
$task1 = new TeachingTask($_POST['pref1']); print '&nbsp;&nbsp; 1: '; $task1->printTaTask();
$task2 = new TeachingTask($_POST['pref2']); print '&nbsp;&nbsp; 2: '; $task2->printTaTask();
$task3 = new TeachingTask($_POST['pref3']); print '&nbsp;&nbsp; 3: '; $task3->printTaTask();
print '</p>';

if (isset($_POST['Comment'])) {
  $comment = addslashes($_POST['Comment']);
  print "<p>&nbsp;&nbsp;  Comment: $comment</p>";
}

// test whether selection is valid
if (testSelection($_POST['pref1'],$_POST['pref2'],$_POST['pref3'])) {
  // In case this is a choice made by an administrator for a student
  if (isMaster() && isset($_POST['email'])) {
     $email = makeEmail($_POST['email']);
  }
  print '<p>Selection is valid. ';

  $row = array();
  $row[0] = $term;
  $row[1] = $email;
  $row[2] = $_POST['pref1'];
  $row[3] = $_POST['pref2'];
  $row[4] = $_POST['pref3'];
  $row[5] = $comment;
  $preference = Preference::fromRow($row);
  if (!isset($preferences->list[$preference->email]))
    $preference->addToDb();
  else
    $preference->updateDb();

  print 'Selection has been registered.</p>';
  print '</select></td></tr>';
}
else {
  print '<p> Selection is NOT valid.<br>';
  print 'Please go back and make sure to select 1-3 distinct preferences.</p>';
}

print '</article>'."\n";

include("app/views/ta/footer.php");

?>
